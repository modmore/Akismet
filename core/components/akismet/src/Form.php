<?php

namespace modmore\Akismet;

use modmore\Akismet\Exceptions\MissingContentException;

class Form {

    /** @var string $type */
    private $type;

    /** @var string $author */
    private $author;

    /** @var string $authorEmail */
    private $authorEmail;

    /** @var string $authorUrl */
    private $authorUrl;

    /** @var string $content */
    private $content;

    /** @var array $extraFields */
    private $extraFields;

    /**
     * @throws MissingContentException
     */
    public function __construct(string $content, array $extraFields)
    {
        $this->content = $content;
        foreach ($extraFields as $key => $value) {
            switch ($key) {

                case 'type':
                    $this->type = $value;
                    break;

                case 'author':
                    $this->author = $value;
                    break;

                case 'author_email':
                    $this->authorEmail = $value;
                    break;

                case 'author_url':
                    $this->authorUrl = $extraFields['author_url'];
                    break;

                default:
                    $this->extraFields[$key] = $value;
            }
        }

        $this->_checkRequired();
    }

    /**
     * @throws MissingContentException
     */
    private function _checkRequired()
    {
        // The only required field is content
        if (!$this->content) {
            throw new MissingContentException('Content field is missing. We have nothing to send to Akismet!');
        }
    }

    public function getFields()
    {

    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }

    /**
     * @return string
     */
    public function getAuthorUrl(): string
    {
        return $this->authorUrl;
    }

    /**
     * @return array
     */
    public function getExtraFields(): array
    {
        return $this->extraFields;
    }
}