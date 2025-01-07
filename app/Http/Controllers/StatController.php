<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StatController extends Controller
{
    public function show($form_id)
    {
        $form = Form::findOrFail($form_id);

        return view('stat.show', [
            'form' => $form,
        ]);
    }

    public function table(Request $request)
    {
        $form = Form::findOrFail($request->id);
        $columns = [];
        foreach($form->questions->sortBy('created_at') as $question) {
            $columns[] = $question->column_name;
        }
    
        $orderBy = empty(request()->input("order.0.column")) ? 'id' : (isset($columns[request()->input("order.0.column")]) ? $columns[request()->input("order.0.column")] : 'id');
        $ord = empty(request()->input("order.0.dir")) ? 'desc' : request()->input("order.0.dir");
        
        $data = DB::table($form->table_name)
            ->join('users', 'users.id', '=', 'user_id')
            ->select($form->table_name.'.*', 'users.name as user_name')
            ->whereNotNull('submitted_at');

        if(request()->input('search.value')) {
            $data = $data->where( function($query) use ($form) {
                foreach($form->questions->sortBy('created_at') as $question) {
                    $query->orWhereRaw($question->column_name.' LIKE ?', ['%'.request()->input('search.value').'%']);
                }
            });
        }

        $recordsFiltered = $data->get()->count();
       
        $data = $data->skip(request()->input('start'))
            ->take(request()->input('length'))
            ->orderBy($orderBy, $ord)
            ->get();

        $recordsTotal = $data->count();

        return response()->json([
            'draw' => request()->input('draw'),
            'recordsTotal' => (int)$recordsTotal,
            'recordsFiltered' => (int)$recordsFiltered,
            'data' => $data
        ]);

    }

    public function xlsx($id)
    {
        $form = Form::findOrFail($id);
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $headText = 'FORM : '.$form->name;

        $activeWorksheet->setCellValue('A1', $headText);
        $activeWorksheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ]);

        $columns = [];
        foreach($form->questions->sortBy('created_at') as $question) {
            $columns[] = $question->column_name;
        }
            
        $data = DB::table($form->table_name)
            ->join('users', 'users.id', '=', 'user_id')
            ->select($form->table_name.'.*', 'users.name as user_name')
            ->whereNotNull('submitted_at')->get();

        // dd($data);
        //3 untuk header
        $columnLetter = 'A';
        $questions = $form->questions->sortBy('created_at');
        foreach($questions as $question) {
            $activeWorksheet->setCellValue($columnLetter.'3', $question->question);
            $activeWorksheet->getStyle($columnLetter.'3')->applyFromArray($styleArray);
            $activeWorksheet->getColumnDimension($columnLetter)->setAutoSize(true);
            $columnLetter++;
        }

        $i = 4;
        foreach ($data as $d) {
            $columnLetter = 'A';
            foreach($questions as $question) {
                if($question->type == 'file') {
                    $urlStr = '';
                    if(!empty($d->{$question->column_name})) {
                        $dt = json_decode($d->{$question->column_name});
                        foreach($dt as $filename) {
                            $urlStr .= url('/storage/'.$form->id.'/'.$filename).' | '.PHP_EOL;
                        }
                    }
                    $activeWorksheet->setCellValue($columnLetter . $i, $urlStr);
                } elseif($question->type == 'checkboxes') {
                    $str = '';
                    if(!empty($d->{$question->column_name})) {
                        $dt = json_decode($d->{$question->column_name});
                        foreach($dt as $item) {
                            $str .= $item.' | '.PHP_EOL;
                        }
                    }
                    $activeWorksheet->setCellValue($columnLetter . $i, $str);
                } else {
                    $activeWorksheet->setCellValue($columnLetter . $i, $d->{$question->column_name});
                }
                $columnLetter++;
            }
            $i++;
        }

        $filename = 'form-' . $form->name . '-' . date('Y-m-d') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
}
