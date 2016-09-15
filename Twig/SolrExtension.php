<?php

namespace ZQ\SunSearchBundle\Twig;

/**
 * Class SolrExtension
 */
class SolrExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('solr_pretty_params', [$this, 'formatParams'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @param string $params
     *
     * @return mixed
     */
    public function formatParams($params)
    {
        return str_replace('{$break}', '<br />', htmlentities(str_replace('&', '{$break}&', $params)));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sunsearch_extension';
    }
}