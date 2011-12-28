<?php

/**
 * MIT licenced
 */

namespace Noop\Bayes\Tokenizer;

class Html extends String
{
    /**
     * Parse only meta and title tags
     */
    const POLICY_METAS = 1;
    const POLICY_HEADERS = 2;
    const POLICY_TITLES = 4;
    const POLICY_IMAGES = 8;
    const POLICY_TEXTS = 16;
    const POLICY_LINKS = 32;

    /**
     * Built-in parser policies
     * @var array
     */
    protected $policies = array(
        self::POLICY_METAS => array('//title',
            '//meta[@name="keywords"]/@content',
            '//meta[@name="description"]/@content'),
        self::POLICY_HEADERS => array('//h1',
            '//h2', '//h3', '//h4', '//h5', '//h6'),
        self::POLICY_TITLES => array('//@title'),
        self::POLICY_IMAGES => array('//img[@alt]/@alt'),
        self::POLICY_TEXTS => array('//p', '//span', '//div', '//b', '//i', '//strong', '//blockquote'),
        self::POLICY_LINKS => array('//a')
    );

    /**
     * Token paths (XPath) that are taken in account
     * @var array
     */
    protected $tokenPaths;

    public function __construct()
    {
        // sets default paths and call parent construct
        $this->setPolicy(self::POLICY_METAS);

        parent::__construct();
    }

    /**
     * Sets parsing policy, can by any binary combination of builtin constants
     * @param int $policy
     */
    public function setPolicy($policy)
    {
        $policyPaths = array();

        foreach ($this->policies as $key => $paths) {
            if ($key & $policy) {
                $policyPaths = array_merge($policyPaths, $paths);
            }
        }

        $this->setTokenPaths($policyPaths);
    }

    /**
     * Tokenizes text; make sure you've set needed paths, because tokenizer doesn't
     * take in account all nodes but text nodes; so, if you match //p nodes,
     * <p>
     *   <b>foo boo</b>
     * </p> - foo boo text won't be taken in account
     * @param string $html
     * @return TokenArray
     */
    public function tokenize($html)
    {
        $dom = new \DOMDocument(1.0, $this->getEncoding());
        @$dom->loadHTML($html);

        $xpath = new \DOMXPath($dom);

        $texts = array();

        foreach ($this->getTokenPaths() as $path) {
            $tags = $xpath->query($path);
            for ($i = 0; $i < $tags->length; $i ++) {
                // remove children instead of text so they dont mess
                $tagNode = $tags->item($i);

                foreach ($tagNode->childNodes as $childNode) {
                    if (!$childNode instanceof \DOMText) {
                        $tagNode->removeChild($childNode);
                    }
                }

                $texts[] = $tagNode->nodeValue;
            }
        }

        $text = implode(' ', $texts);

        return parent::tokenize($text);
    }

    public function getTokenPaths()
    {
        return $this->tokenPaths;
    }

    public function setTokenPaths($tokenPaths)
    {
        $this->tokenPaths = $tokenPaths;
    }
}
