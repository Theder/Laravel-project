<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Theme\Directory;
use App\Http\Requests\Common\ThemeEdtorUpdate;

class ThemeEditorController extends Controller
{
    /**
     * List of templates
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $directory = new Directory(base_path() . '/resources/views');
        $directories = $directory->scanDirRecursively();
        
        return view('admin.theme.index', compact('directories'));
    }

    /**
     * Show the form of template editting
     * 
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $directory = new Directory(base_path() . '/resources/views');
        $directories = $directory->scanDirRecursively();

        $fileName = $request->input('file');
        $fileContent = file_get_contents(base_path() . '/resources/views/' . $fileName);

        return view('admin.theme.edit', compact('directories', 'fileName', 'fileContent'));
    }

    /**
     * Update the specific template
     * 
     * @param \App\Http\Requests\Common\ThemeEdtorUpdate  $request
     * @return \Illuminate\Http\Response
     */
    public function update(ThemeEdtorUpdate $request)
    {
        $data = $request->validated();

        $sourceCode = $data['source_code'];
        $fileName = $data['filename'];

        file_put_contents(base_path() . '/resources/views/' . $fileName, $sourceCode);

        return redirect()->route('theme.editor.edit', ['file' => $fileName])
            ->with(['status' => 'File successfully updated']);
    }
}
