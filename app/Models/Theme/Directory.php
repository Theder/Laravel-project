<?php

namespace App\Models\Theme;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    use HasFactory;

    private $dirName;

    public function __construct($dirName)
    {
        $this->dirName = $dirName;
    }

    /**
     * Scan directory and subdirectory for template files
     * 
     * @return array
     */
    public function scanDirRecursively()
    {
        $diretoryIter = new \RecursiveDirectoryIterator(base_path() . '/resources/views');
        $iterator = new \RecursiveIteratorIterator($diretoryIter, \RecursiveIteratorIterator::CHILD_FIRST);
        $result = [];

        foreach ($iterator as $splFileInfo) {
            if ($splFileInfo->getFilename() === '.' || $splFileInfo->getFilename() === '..')
                continue;

            $path = $splFileInfo->isDir()
                    ? array($splFileInfo->getFilename() => array())
                    : array($splFileInfo->getFilename());

            for ($depth = $iterator->getDepth() - 1; $depth >= 0; $depth--) {
                $path = array($iterator->getSubIterator($depth)->current()->getFilename() => $path);
            }

            $result = array_merge_recursive($result, $path);
        }

        arsort($result);

        return $result;
    }
}
