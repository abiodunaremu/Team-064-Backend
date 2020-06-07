<?php
namespace Lib\ResponseBuilder;

class JsonHandler
{
    public function adjustJsonString($value)
    {
        return str_replace("\'", "\\\'", str_replace("\"", "\\\"", str_replace("\n", "\\n", $value)));
    }

    public function adjustSQLString($value)
    {
        return str_replace("'", "\'", str_replace("\"", "\\\"", $value));
    }

    public function quoteString($value)
    {
        return "\"".$value."\"";
    }
}
