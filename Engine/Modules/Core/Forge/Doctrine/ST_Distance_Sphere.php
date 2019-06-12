<?php
/**
 * Created by PhpStorm.
 * User: motte
 * Date: 12.06.2019
 * Time: 09:13
 */


/* This file is auto-generated. Don't edit directly! */
namespace  Oforge\Engine\Modules\Core\Forge\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class ST_Distance_Sphere extends FunctionNode
{
    protected $expressions = [];
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expressions[] = $parser->ArithmeticFactor();
        $parser->match(Lexer::T_COMMA);
        $this->expressions[] = $parser->ArithmeticFactor();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
    public function getSql(SqlWalker $sqlWalker)
    {
        $arguments = [];
        foreach ($this->expressions as $expression) {
            $arguments[] = $expression->dispatch($sqlWalker);
        }
        return 'ST_Distance_Sphere(' . implode(', ', $arguments) . ')';
    }
}