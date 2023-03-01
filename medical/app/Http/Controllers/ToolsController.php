<?php

namespace App\Http\Controllers;

use App\Models\User;
use Excel;
use Illuminate\Support\Facades\Storage;

class ToolsController extends Controller
{
    /**
     * @param $file
     * @return mixed | path to saved file with file original name
     */
    protected function saveFiles($file)
    {

        $file_path = $file;
        $file_name = $file_path->getClientOriginalName();
        $uploaded_path = $file->storeAs('uploads', $file_name, 'public');
        return $uploaded_path;
    }

    /**
     * Return data from uploaded files based on given triggers
     *
     * @param $file_path
     * @param string $type - ALL|HEADERS
     * @param int $limit use -1 if need ger all rows with offset
     * @param int $offset
     * @return array
     */
    protected function getDataFromFile($file_path, $type = "ALL", $limit = 0, $offset = 0, $header_row = 0)
    {

        $counter = 0;
        $cols_number = 0;
        $file_data = Excel::toArray([], Storage::disk('public')->path($file_path));
        if ($type == "HEADERS") {
            $header_row = $offset;
        }
        foreach ($file_data[0][$header_row] as $value) {
            if (!empty($value)) {
                $cols_number++;
            }
        }
        $result_array = [];
        if ($type == "HEADERS") {
            foreach ($file_data[0][$offset] as $key => $value) {
                if ($key < $cols_number) {
                    $result_array[$key] = $value;
                } else {
                    break;
                }
            }
        } else {
            foreach ($file_data[0] as $key => $value) {
                if ($counter == $limit) {
                    break;
                }
                if ($counter < $offset) {
                    $counter++;
                    continue;
                } else {
                    $result_array[$key] = array_slice($value, 0, $cols_number);
                }
                $counter++;
            }
        }
        return $result_array;
    }

    /**
     * Replace String what contains transaction number to transaction number in it
     *
     * @param $transfer_array
     * @param $transfer_col_number
     * @return array
     */
    protected function replaceTransferId($transfer_array, $transfer_col_number)
    {
        $counter = 0;
        $result_array = [];
        foreach ($transfer_array as $transfer_item) {
            $tmp = preg_split("/TRN\*[0-9]{1,}\*/", $transfer_item[$transfer_col_number], 2);
            if (!isset($tmp[1])) {
                $tmp = $tmp[0];
            } else {
                $tmp = preg_replace("/\*.+/", "", $tmp[1]);
            }
            $result_array[$counter] = $transfer_item;
            $result_array[$counter][$transfer_col_number] = $tmp;
            $counter++;
        }
        return $result_array;
    }

    /* protected function calculateTotals($target_array, $matches_array, $target_key, $match_key, $target_value, $match_value, $result_array = [], $counter = 0){
        foreach ( $target_array as $t_key => $t_value) {
            $result_array[$counter]['amount'] = -1;
            $sum = 0;
            $matches_counter = 0;
            foreach ($matches_array as $m_key => $m_value) {
                if ($t_value[$target_key] == $m_value[$match_key]){
                    $result_array[$counter]['row_number'] = $m_key;
                    $result_array[$counter]['text'] = "Subtotal";
                    $result_array[$counter]['transaction_id'] = $m_value[$match_key];
                    $sum += preg_replace('/,/', '.', $m_value[$match_value]);
                    $result_array[$counter]['amount'] = round($sum, 2);
                    if($target_value == -1){
                        $result_array[$counter]['paid'] = 0;
                    } else {
                        $result_array[$counter]['paid'] = round(preg_replace('/,/', '.', $t_value[$target_value]), 2);
                    }
                    $result_array[$counter]['difference'] = round($result_array[$counter]['amount'] + $result_array[$counter]['paid'], 2);
                    $matches_counter++;
                    $result_array[$counter]['rows_count'] = $matches_counter;
                    unset($matches_array[$m_key]);
                }
            }
            if($result_array[$counter]['amount'] != -1){
                $counter++;
            }
        }

        $result_array['counter'] = $counter-1;
        return $result_array;
    }
*/
    protected function debug_table($array)
    {
        echo '<table border="1">';
        foreach ($array as $row_key => $row_value) {
            echo "<tr>";
            foreach ($row_value as $col_key => $col_value) {
                echo "<td>" . $col_value . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    protected function formatKeys($string)
    {
        $string = strtolower($string);
        $string = preg_replace("/\s+/", "_", $string);
        return $string;
    }

    protected function rebuild2DArray($data_array, $keys_array, $prefix = "")
    {
        $result_array = [];
        foreach ($data_array as $data_key => $data_value) {
            foreach ($data_value as $key => $value) {
                if (!empty($prefix)) {
                    $result_array[$data_key][$prefix . $this->formatKeys($keys_array[$key])] = $value;
                } else {
                    $result_array[$data_key][$this->formatKeys($keys_array[$key])] = $value;
                }
            }
        }
        return $result_array;
    }

    protected function blockSplit($data_array, $transaction_code_key)
    {
        $sorting_array = [];
        $counter = 0;
        foreach ($data_array as $row_key => $row) {
            $transaction_code = $row[$transaction_code_key];
            foreach ($data_array as $inner_key => $inner_row) {
                if ($transaction_code == $inner_row[$transaction_code_key]) {
                    $sorting_array['block_' . $counter][] = $inner_row;
                    unset($data_array[$inner_key]);
                }
            }
            $counter++;
        }
        return ($sorting_array);
    }

    protected function reformatAmount($amount)
    {

        $amount = round(preg_replace('/,/', '.', $amount), 2);
        return $amount;
    }

    protected function addTotals($data_array, $transaction_id_key, $left_amount_key, $right_amount_key)
    {

        //dump($data_array);
        $cols_number = count($data_array['block_0'][0]);
        $empty_array = [];
        for ($i = 0; $i <= $cols_number - 5; $i++) {
            $empty_array[$i] = "";
        }
        foreach ($data_array as $block_key => $block) {

            $total_row['text'] = "Subtotal";
            $total_row['transaction_id'] = "";
            $total_row['left_total'] = 0;
            $total_row['right_total'] = 0;
            $total_row['difference'] = 0;
            if (count($block) > 1) {
                foreach ($block as $key => $value) {
                    $total_row['left_total'] += $this->reformatAmount($value[$left_amount_key]);
                    $total_row['right_total'] = $this->reformatAmount($value[$right_amount_key]);
                    $total_row['transaction_id'] = $value[$transaction_id_key];
                    $data_array[$block_key][$key]['difference'] = "";
                }
                $total_row['left_total'] = $this->reformatAmount($total_row['left_total']);
                $total_row['right_total'] = $this->reformatAmount($total_row['right_total']);

                $total_row['difference'] = $this->reformatAmount(
                    $this->reformatAmount($total_row['left_total'])
                    +
                    $this->reformatAmount($total_row['right_total'])
                );
                $data_array[$block_key][count($block)] = array_merge($empty_array, $total_row);
            }
        }
        return $data_array;
    }

    protected function reformatHeaders($headers_array, $left_transaction_id_key, $right_transaction_id_key, $left_amount_key, $right_amount_key)
    {
        $tmp_headers_array = $headers_array;
        unset($tmp_headers_array[$right_transaction_id_key]);
        unset($tmp_headers_array[$left_amount_key]);
        unset($tmp_headers_array[$right_amount_key]);
        $tmp_headers_array[$left_amount_key] = $headers_array[$left_amount_key];
        $tmp_headers_array[$right_amount_key] = $headers_array[$right_amount_key];
        return $tmp_headers_array;
    }

    protected function separateResult($data_array, $search_key, $search_value){
/*
        dump($data_array);
        dump($search_key);
        dump($search_value);
*/
        $base_array = [];
        $separated_array = [];

        foreach($data_array as $data_key => $data_row){

            if(array_key_exists($search_key, $data_row)) {
                if (in_array($data_row[$search_key], $search_value)) {
                    $separated_array[] = $data_row;
                } else {
                    $base_array[] = $data_row;
                }
            } else {
                $base_array[] = $data_row;
            }
        }

        $result = [
            'base'      => $base_array,
            'separated' => $separated_array
        ];

        return $result;
    }

    protected function reformatArray($data_array, $left_transaction_id_key, $right_transaction_id_key, $left_amount_key, $right_amount_key)
    {
        $result_array = [];
        $result_counter = 0;
        foreach ($data_array as $block_key => $block) {
            $block_items = count($block);
            $tmp_array = [];
            if ($block_items > 1) {
                foreach ($block as $key => $row) {
                    //dump($key);
                    //dump($block_items);
                    if ($key != $block_items - 1) {
                        $result_array[$result_counter] = $row;
                        $tmp_array[$left_amount_key] = $result_array[$result_counter][$left_amount_key];
                        if ($key == $block_items - 2) {
                            $tmp_array[$right_amount_key] = $result_array[$result_counter][$right_amount_key];
                        } else {
                            $tmp_array[$right_amount_key] = "";
                        }
                        $tmp_array['difference'] = "";
                        unset($result_array[$result_counter][$right_transaction_id_key]);
                        unset($result_array[$result_counter][$left_amount_key]);
                        unset($result_array[$result_counter][$right_amount_key]);
                        unset($result_array[$result_counter]['difference']);
                        $result_array[$result_counter] = array_merge($result_array[$result_counter], $tmp_array);
                    } else if ($key == $block_items - 1) {
                        $result_array[$result_counter] = $row;
                        unset($result_array[$result_counter][0]);
                    }
                    $result_counter++;
                }
            } else if (count($block) == 1) {
                $result_array[$result_counter] = $block[0];
                if (empty($result_array[$result_counter][$left_amount_key])) {
                    $tmp_array[$left_amount_key] = 0;
                } else {
                    $tmp_array[$left_amount_key] = $result_array[$result_counter][$left_amount_key];
                }
                if (empty($result_array[$result_counter][$right_amount_key])) {
                    $tmp_array[$right_amount_key] = 0;
                } else {
                    $tmp_array[$right_amount_key] = $result_array[$result_counter][$right_amount_key];
                }
                $tmp_array['difference'] = $this->reformatAmount(
                    $this->reformatAmount($result_array[$result_counter][$left_amount_key])
                    +
                    $this->reformatAmount($result_array[$result_counter][$right_amount_key])
                );
                unset($result_array[$result_counter][$right_transaction_id_key]);
                unset($result_array[$result_counter][$left_amount_key]);
                unset($result_array[$result_counter][$right_amount_key]);
                $result_array[$result_counter] = array_merge($result_array[$result_counter], $tmp_array);
                $result_counter++;
            }
        }
        //dump($result_array);
        return $result_array;
    }

    protected function calculateColumnTotal($array, $column){

        $total = 0;
        //dump($column);
        foreach($array as $row){
            //dump($row);
            if(isset($row[$column])) {
                $total += $this->reformatAmount($row[$column]);
            }

        }
        //die();
        return $this->reformatAmount($total);
    }

    protected function fullMergeArrays($left_array, $left_array_keys, $right_array, $right_array_keys, $left_transaction_code, $right_transaction_code, $left_amount, $right_amount)
    {

        $combined_array = [];
        $left_prefix = "left_";
        $right_prefix = "right_";
        $left_array = $this->rebuild2DArray($left_array, $left_array_keys, $left_prefix);
        $right_array = $this->rebuild2DArray($right_array, $right_array_keys, $right_prefix);
        $left_headers[] = $left_array_keys;
        $right_headers[] = $right_array_keys;
        $left_headers = $this->rebuild2DArray($left_headers, $left_array_keys, $left_prefix);
        $right_headers = $this->rebuild2DArray($right_headers, $right_array_keys, $right_prefix);
        $headers = array_merge($left_headers[0], $right_headers[0]);
        $empty_array = [];
        foreach ($right_array_keys as $key => $value) {
            $empty_array[$right_prefix . $this->formatKeys($value)] = "";
        }
        foreach ($left_array as $left_key => $left_row) {
            $row_found = FALSE;
            foreach ($right_array as $right_key => $right_row) {
                if ($left_row[$left_prefix . $this->formatKeys($left_array_keys[$left_transaction_code])] == $right_row[$right_prefix . $this->formatKeys($right_array_keys[$right_transaction_code])]) {
                    $combined_array[$left_key] = array_merge($left_row, $right_row);
                    $row_found = TRUE;
                    break;
                }
            }
            if (!$row_found) {
                $combined_array[$left_key] = array_merge($left_row, $empty_array);
            }
        }
        $reformatted_array = $this->blockSplit(
            $combined_array,
            $left_prefix . $this->formatKeys($left_array_keys[$left_transaction_code])
        );
        $reformatted_array_with_totals = $this->addTotals(
            $reformatted_array,
            $left_prefix . $this->formatKeys($left_array_keys[$left_transaction_code]),
            $left_prefix . $this->formatKeys($left_array_keys[$left_amount]),
            $right_prefix . $this->formatKeys($right_array_keys[$right_amount])
        );
        $headers_array = $this->reformatHeaders(
            $headers,
            $left_prefix . $this->formatKeys($left_array_keys[$left_transaction_code]),
            $right_prefix . $this->formatKeys($right_array_keys[$right_transaction_code]),
            $left_prefix . $this->formatKeys($left_array_keys[$left_amount]),
            $right_prefix . $this->formatKeys($right_array_keys[$right_amount])
        );
        $result_array = $this->reformatArray(
            $reformatted_array_with_totals,
            $left_prefix . $this->formatKeys($left_array_keys[$left_transaction_code]),
            $right_prefix . $this->formatKeys($right_array_keys[$right_transaction_code]),
            $left_prefix . $this->formatKeys($left_array_keys[$left_amount]),
            $right_prefix . $this->formatKeys($right_array_keys[$right_amount])
        );
        $headers_array['difference'] = 'Total';
        $result_array['data'] = $result_array;
        $result_array['headers'] = $headers_array;
        return $result_array;
    }

    protected function removeUnneededData2D($data_array, $keep_items)
    {

        $keep_items[] = 'difference';
        //dump($keep_items);
        //dump($data_array);
        $keep_items_count = count($keep_items);
        foreach ($data_array as $key => $row) {
            if (array_key_exists("text", $row)) {
                $array_items_count = count($row);
                for ($i = 1; $i <= ($array_items_count - $keep_items_count + 1); $i++) {
                    if (isset($data_array[$key][$i])) {
                        unset($data_array[$key][$i]);
                    }
                }
            } else {
                foreach ($row as $r_key => $r_value) {
                    if (!in_array($r_key, $keep_items)) {
                        unset($data_array[$key][$r_key]);
                    }
                }
            }
        }
        return $data_array;
    }

    protected function CheckUserRole($user_id, $role){

        $user_obj = User::find($user_id);
        $role_slug = $user_obj->role()->first()->slug;

        if($role_slug == $role){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    protected function UserHasRole($user, $role){

        //$user = User::find($user_id);
        $roles = $user->role()->first();
        if($roles->slug === $role){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * Return merged arrays based on given data. Right file data will be added to left file data
     * based on accordance transaction codes.
     *
     * @param $left_array - data array from file uploaded on left form side (Loans file)
     * @param $right_array - data array from file uploaded on right side (Payments file)
     * @param $left_transaction_code - left file transaction code column number
     * @param $right_transaction_code - Right file transaction code column number
     * @param $right_amount - Right file amount column number
     * @return array
     */
    /*
    protected function mergeArrays($left_array, $right_array, $left_transaction_code, $right_transaction_code, $left_amount, $right_amount)
    {

        $loans_array = $this->calculateTotals(
            $left_array,
            $left_array,
            $left_transaction_code,
            $left_transaction_code,
            -1,
            $left_amount
        );

        $loans_array = $this->calculateTotals(
            $right_array,
            $left_array,
            $right_transaction_code,
            $left_transaction_code,
            $right_amount,
            $left_amount,
            $loans_array,
            $loans_array['counter']
        );

        unset($loans_array['counter']);
        $re_loans_array = [];
        foreach($loans_array as $item){
            if(isset($item['row_number'])){
                $re_loans_array[$item['row_number']] = $item;
            }
        }

        $elements_number = count(reset($left_array));

        $combined_array = [];
        foreach($left_array as $key => $item){
            $combined_array[$key] = $item;
            $combined_array[$key][$elements_number] = " ";
            $combined_array[$key][$elements_number+1] = " ";
        }

        $prepared_array = [];
        $counter = 0;
        foreach($combined_array as $key => $row){

            $prepared_array[$counter] = $row;

            if(array_key_exists($key, $re_loans_array)){

                if($re_loans_array[$key]['rows_count'] > 1) {
                    $insert = [];
                    for ($i = 0; $i < $elements_number - 2; $i++) {
                        $insert[$i] = " ";
                    }
                    $insert[$elements_number - 3] = $re_loans_array[$key]['text'];
                    $insert[$elements_number - 2] = $re_loans_array[$key]['transaction_id'];
                    $insert[$elements_number - 1] = $re_loans_array[$key]['amount'];
                    $insert[$elements_number] = $re_loans_array[$key]['paid'];
                    $insert[$elements_number + 1] = $re_loans_array[$key]['difference'];
                    $prepared_array[$counter + 1] = $insert;
                    $counter++;
                } else {
                    $prepared_array[$counter][$elements_number] = $re_loans_array[$key]['paid'];
                    $prepared_array[$counter][$elements_number+1] = $re_loans_array[$key]['difference'];
                }
            }

            $counter++;
        }

        return $prepared_array;
    }
    */
}
