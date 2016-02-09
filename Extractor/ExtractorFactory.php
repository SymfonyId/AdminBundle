<?php

namespace Symfonian\Indonesia\AdminBundle\Extractor;

class ExtractorFactory
{
    private $extractors = array();

    private $freeze = false;

    public function addExtractor(ExtractorInterface $extractor)
    {
        $this->extractors[get_class($extractor)] = $extractor;
    }

    public function getExtractor($name)
    {
        if (!array_key_exists($name, $this->extractors)) {
            throw new \InvalidArgumentException(sprintf('Extractor for %s not found.', $name));
        }

        return $this->extractors[$name];
    }

    public function freeze()
    {
        $this->freeze = true;
    }
}