<?php

if (!function_exists('alert_failed')) {
    function alert_failed($html)
    {
        $alert = '<div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        ' . $html . '
                    </div>';
        return $alert;
    }
}

if (!function_exists('alert_success')) {
    function alert_success($html)
    {
        $alert = '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        ' . $html . '
                    </div>';
        return $alert;
    }
}

function cek_id($table, $col, $val)
{
    $sql = "SELECT * FROM $table WHERE $col = ? ORDER BY id DESC LIMIT 1";
    $ci = &get_instance();
    $cek = $ci->db->query($sql, [$val])->num_rows();
    if ($cek > 0) {
        return time();
    } else {
        return $val;
    }
}

// function getNextOrderNumber()
// {
    // Get the last created order
    // $lastOrder = Order::orderBy('created_at', 'desc')->first();

    // if (!$lastOrder)
        // We get here if there is no order at all
        // If there is no number set it to 0, which will be 1 at the end.

    //     $number = 0;
    // else
    //     $number = substr($lastOrder->order_id, 3);

    // If we have ORD000001 in the database then we only want the number
    // So the substr returns this 000001

    // Add the string in front and higher up the number.
    // the %05d part makes sure that there are always 6 numbers in the string.
    // so it adds the missing zero's when needed.

//     return 'ORD' . sprintf('%06d', intval($number) + 1);
// }
