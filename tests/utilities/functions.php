<?php

function create($class, $attributes = [], $states = [], $times = null)
{
    return factory($class, $times)->states($states)->create($attributes);
}

function make($class, $attributes = [], $states = [], $times = null)
{
    return factory($class, $times)->states($states)->make($attributes);
}
