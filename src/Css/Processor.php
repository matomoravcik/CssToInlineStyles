<?php

namespace MatoMoravcik\CssToInlineStyles\Css;

use MatoMoravcik\CssToInlineStyles\Css\Rule\Processor as RuleProcessor;

class Processor
{
    /**
     * Get the rules from a given CSS-string
     *
     * @param string $css
     * @param array  $existingRules
     * @return Rule[]
     */
    public function getRules($css, $existingRules = array())
    {
        $css = $this->doCleanup($css);
        $rulesProcessor = new RuleProcessor();
        $rules = $rulesProcessor->splitIntoSeparateRules($css);

        return $rulesProcessor->convertArrayToObjects($rules, $existingRules);
    }

    /**
     * Get the CSS from the style-tags in the given HTML-string
     *
     * @param string $html
     * @return string
     */
    public function getCssFromStyleTags($html)
    {
        $css = '';
        $matches = array();
        preg_match_all('|<style(.*)>(.*)</style>|isU', $html, $matches);

        if (!empty($matches[2])) {
            foreach ($matches[2] as $match) {
                $css .= trim($match) . "\n";
            }
        }

        return $css;
    }

    /**
     * @param string $css
     * @return string
     */
    private function doCleanup($css)
    {
        // remove charset
        $css = preg_replace('/@charset "[^"]+";/', '', $css);
        // remove media queries
        $css = preg_replace('/@media [^{]*{([^{}]|{[^{}]*})*}/', '', $css);

        $css = str_replace(array("\r", "\n"), '', $css);
        $css = str_replace(array("\t"), ' ', $css);
        $css = str_replace('"', '\'', $css);
        $css = preg_replace('|/\*.*?\*/|', '', $css);
        $css = preg_replace('/\s\s+/', ' ', $css);
        $css = trim($css);

        return $css;
    }
}
