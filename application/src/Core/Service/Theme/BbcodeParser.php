<?php
/**
 * Author: Alex P.
 * Date: 18.04.14
 * Time: 13:56
 */

namespace Core\Service\Theme;


class BbcodeParser implements \Ikantam\Theme\Interfaces\BbcodeParserInterface
{
    protected  $parser;

    public function __construct(\JBBCode\Parser $parser, array $customBbcodes = array())
    {
        $this->parser = $parser;
        $this->addBBcodes($this->parser, $customBbcodes);

    }

    /**
     * Convert bbcode to html
     * @param string $bbcodeString
     * @return string
     */
    public function getAsHtml($bbcodeString)
    {
        $parser = $this->getParser();
        $parser->parse($bbcodeString);

        return $parser->getAsHTML();
    }

    public function getParser()
    {
        return $this->parser;
    }

    protected function addBbcodes($parser, $customBbcodes)
    {
        foreach ($customBbcodes as $name => $value) {
            $builder = new \JBBCode\CodeDefinitionBuilder($name, $value);
            $parser->addCodeDefinition($builder->build());
        }
    }
}