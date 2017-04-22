<?php

namespace Sifo\Filesystem;

/**
 * Filters out unwanted files from the iterator. Pass an array with the list of
 * extensions you want to accept.
 */
class FilterFilesByExtensionIterator extends \FilterIterator
{

    protected $allowed_extensions;

    public function __construct(\Iterator $iterator, Array $allowed_extensions)
    {
        $this->allowed_extensions = $allowed_extensions;
        parent::__construct($iterator);
    }

    /**
     * Checks if current file matches the allowed extension or not. If directories
     * are passed will be accepted.
     *
     * @return boolean
     */
    public function accept()
    {
        // If directories are passed, we accept them. Is duty of the calling function:

        if ($this->current()->isDir())
        {
            return true;
        }

        if (!empty($this->allowed_extensions))
        {
            foreach ($this->allowed_extensions as $ext)
            {
                if (pathinfo($this->getBaseName(), PATHINFO_EXTENSION) == $ext)
                {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

}
