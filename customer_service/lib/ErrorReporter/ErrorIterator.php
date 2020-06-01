<?php
namespace Lib\ErrorReporter;

interface ErrorIterator
{
    // static function setHeadNode();
    public static function getHeadNode();
    // static function setTailNode();
    public static function getTailNode();
    public static function getNodeCount();
}
