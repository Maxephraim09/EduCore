<?php
function get_points($marks) {
    $grades = $GLOBALS['divisions'];
    $points = 0;

    $marks_array = is_array($marks) ? $marks : [$marks];

    foreach ($marks_array as $mark) {
        foreach ($grades as $gradee) {

            // Safe fallback: check if key exists
            $min_mark = isset($gradee['min_mark']) ? floatval($gradee['min_mark']) 
                        : (isset($gradee[1]) ? floatval($gradee[1]) : 0);

            $max_mark = isset($gradee['max_mark']) ? floatval($gradee['max_mark']) 
                        : (isset($gradee[2]) ? floatval($gradee[2]) : 100);

            $grade_point = isset($gradee['points']) ? floatval($gradee['points']) 
                        : (isset($gradee[5]) ? floatval($gradee[5]) : 0);

            if ($mark >= $min_mark && $mark <= $max_mark) {
                $points += $grade_point;
            }
        }
    }
    return $points;
}

function get_division($marks) {
    $the_points = get_points($marks);
    $divisions = $GLOBALS['divisions'];
    $division = 'N/A';

    foreach ($divisions as $div) {
        $min_point = isset($div['min_point']) ? floatval($div['min_point'])
                    : (isset($div[3]) ? floatval($div[3]) : 0);

        $max_point = isset($div['max_point']) ? floatval($div['max_point'])
                    : (isset($div[4]) ? floatval($div[4]) : 100);

        $division = isset($div['name']) ? $div['name'] 
                    : (isset($div[0]) ? $div[0] : 'N/A');

        if ($the_points >= $min_point && $the_points <= $max_point) {
            break;
        }
    }
    return $division;
}
