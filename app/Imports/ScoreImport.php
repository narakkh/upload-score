<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use DB;
use function Livewire\store;

class ScoreImport implements ToCollection
{
    public function collection(Collection $collection)
    {

        try {
            $html_table = "<table class='styled-table'>";
            $count_0 = 0;
            $count_1 = 0;


            $row_num = 1;
            $code_key = 1;
            $name_key = 2;
            $class_key = 4;
            $start_score_key = 9;
            $start_row_data_key = 5;
            $thead = $collection[2];
            $score_type = $collection[3];
            $score_type_text = '';
            $batch_data = [];
            $get_score_type = function ($type) {
                $type = trim($type);

                return match (true) {
                    str_contains($type, 'កិ.') => 1,
                    str_contains($type, 'សំ.') => 2,
                    str_contains($type, 'ប្រ.') => 3,
                    default => 0
                };
            };

            $html_table .= "<thead><tr'>
            <th>#</th>
            <th>" . $thead[1] . "</th>
            <th>" . $thead[2] . "</th>
            <th>" . $thead[4] . "</th>
            <th>Score type</th>
            <th>Score</th>
            </tr></thead><tbody>";

            foreach ($collection as $row_key => $row) {
                if ($row_key >= $start_row_data_key) {
                    $columnItem = $row->all();
                    foreach ($columnItem as $col_key => $col) {
                        $score = @$collection[$row_key][$start_score_key + $col_key]??null;
                        $meet_type = @$score_type[$start_score_key + $col_key] ?? null;

                        if ($meet_type) {
                            $score_type_text = $meet_type;
                        }

                        $score_num = 0;
                        $score_str = null;
                        if (is_numeric($score)) {
                            $score_num = $score;
                        } else {
                            $score_str = $score;
                        }

                        $item_row = [
                            'student_id' => $collection[$row_key][$code_key],
                            'class_id' => $collection[$row_key][$class_key],
                            'score_type' => $get_score_type($score_type_text),
                        ];
                        if ($score) {
                            $item_row['score_value'] = $score_num;
                            $item_row['score_str'] = $score_str;

                            $batch_data[$row_num] = $item_row;
                            $html_table .= "<tr>
                                        <td>$row_num</td>
                                        <td>" . $item_row['student_id'] . "</td>
                                        <td>" . $collection[$row_key][$name_key] . "</td>
                                        <td>" . $item_row['class_id'] . "</td>
                                        <td>$score_type_text</td>
                                        <td>" . ($score_str?$score_str:$item_row['score_value']) . "</td>
                                        </tr>";
                            $row_num++;
                        }
                        if ($score===0){
                            $item_row['score_value'] = 0;
                            $item_row['score_str'] = null;

                            $html_table .= "<tr>
                                        <td>$row_num</td>
                                        <td>" . $item_row['student_id'] . "</td>
                                        <td>" . $collection[$row_key][$name_key] . "</td>
                                        <td>" . $item_row['class_id'] . "</td>
                                        <td>$score_type_text</td>
                                        <td>0</td>
                                        </tr>";


                            $batch_data[$row_num] = $item_row;
                            $row_num++;
                        }

                    }
                }
            }
            if (count($batch_data)) DB::table('english_score_narak')->insert($batch_data);
            $html_table .= "</tbody></table>";
            return redirect()->back()->with('success', $html_table);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error',$exception->getMessage());
        }

    }
}
