<?php

namespace Oforge\Engine\Modules\Core\Forge\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

use function implode;
use function sprintf;

/**
 * @author Przemek Sobstel <przemek@sobstel.org> https://github.com/beberlei/DoctrineExtensions
 * combined with https://github.com/oroinc/doctrine-extensions/blob/master/src/Oro/ORM/Query/AST/Functions/Numeric/TimestampDiff.php
 */
class TimestampDiff extends FunctionNode
{
    protected const SUPPORTED_UNITS = [
        'MICROSECOND' => 1,
        'SECOND'      => 1,
        'MINUTE'      => 1,
        'HOUR'        => 1,
        'DAY'         => 1,
        'WEEK'        => 1,
        'MONTH'       => 1,
        'QUARTER'     => 1,
        'YEAR'        => 1,
    ];
    public $firstDatetimeExpression = null;
    public $secondDatetimeExpression = null;
    public $unit = null;

    /**
     * @param SqlWalker $sqlWalker
     *
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            'TIMESTAMPDIFF(%s, %s, %s)',
            $this->unit,
            $this->firstDatetimeExpression->dispatch($sqlWalker),
            $this->secondDatetimeExpression->dispatch($sqlWalker)
        );
    }

    /**
     * @param Parser $parser
     *
     * @throws QueryException
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_IDENTIFIER);

        $lexer = $parser->getLexer();
        $unit  = strtoupper(trim($lexer->token['value']));
        if ( !isset(self::SUPPORTED_UNITS[$unit])) {
            $parser->syntaxError(
                sprintf(
                    'Unit %s is not supported by TIMESTAMPDIFF function. The supported units are: "%s"',
                    $unit,
                    implode(', ', array_keys(self::SUPPORTED_UNITS))
                ),
                $lexer->token
            );
        }
        $this->unit = $unit;
        $parser->match(Lexer::T_COMMA);
        $this->firstDatetimeExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->secondDatetimeExpression = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

}
