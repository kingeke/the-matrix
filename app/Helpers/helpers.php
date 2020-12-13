<?php

function messageResponse($status, $message)
{
    return [
        'status' => $status,
        'body'   => $message,
    ];
}

/**
 * Date formater
 *
 * @param string $date
 * @param boolean $withTime
 *
 * @return string
 */
function dateFormat($date, $withTime = true)
{
    return isset($date) ? now()->parse($date)->format($withTime ? 'jS M, Y - h:i A' : 'jS M, Y') : 'Not Available';
}

function formatHack($item)
{
    return str_replace('_', ' ', $item);
}
