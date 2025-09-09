<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class M3U8Controller extends Controller
{
    // private $parentPath = "D:\M3U8";
    private $parentPath = "D:\S24\\video";
    public function getM3u8(){
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "0");
        $directory = $this->parentPath;
        $m3u8Files = [];
        // dd($directory);

        if (File::exists($directory)) {
        // Get all files in the directory with .m3u8 extension
            $files = File::files($directory);

            foreach ($files as $file) {
                if ($file->getExtension() === 'm3u8') {
                    $m3u8Files[] = $file->getFilename();
                }
            }
        } else {

            // throw new Exception("Directory does not exist: $directory");
        }
  
        $m3u8FilesComplete = [];
        foreach($m3u8Files as $file){
            $m3u8ContentFolder = "{$directory }\\{$file}_contents";
            
           
            if (File::exists($m3u8ContentFolder)) {
                $m3u8FilesComplete[]=$file;
            }
     
        }

        // dd($m3u8FilesComplete);
        //process the m3u8 segment replacement
        foreach($m3u8FilesComplete as $c=> $file){
            $filePath = "{$this->parentPath}\\{$file}";
            $content = File::get($filePath);
            $F_filename=$fileName = str_replace(".m3u8","",$file);

            $replace = "-";

            if(strpos($fileName, "–") !== false){

                $F_filename = str_replace("–",$replace,$fileName);
                File::move("{$this->parentPath}\\{$file}", "{$this->parentPath}\\{$F_filename}.m3u8");
                File::move("{$this->parentPath}\\{$file}_contents", "{$this->parentPath}\\{$F_filename}.m3u8_contents");
                $m3u8FilesComplete[$c] = str_replace("–",$replace,$file);
            }
       


            $updatedContent = preg_replace_callback(
                '/file:\/\/\/sdcard\/Download\/UCDownloads\/video\/' . preg_quote($fileName, '/') . '\.m3u8_contents\/(\d+)/',
                function ($matches) use ($fileName) {
                   
                    return "{$fileName}.m3u8_contents/{$matches[1]}.ts";
                },
                $content
            );

            $updatedContent = str_replace("–",$replace,$updatedContent);
            File::put($filePath, $updatedContent);
        }


        //update all segment extension to .ts
        $commandF = "";

        foreach($m3u8FilesComplete as $file){
            $segmentFiles = File::files("{$this->parentPath}\\{$file}_contents");
            $fileName = str_replace(".m3u8","",$file);

      
            foreach ($segmentFiles as $segmentFile) {

                $oldPath = $segmentFile->getPathname();
                $newPath = $oldPath . '.ts';

                // Check if the file already has the .ts extension
                if (!str_ends_with($oldPath, '.ts')) {
                    File::move($oldPath, $newPath);
                    // echo "Renamed: {$segmentFile->getFilename()} -> {$segmentFile->getFilename()}.ts\n";
                } else {
                    // echo "Skipped (already has .ts): {$segmentFile->getFilename()}\n";
                }
            }
            $file_path = "{$this->parentPath}\\{$file}";
            $command = 'ffmpeg -protocol_whitelist "file,http,https,tcp,tls,crypto" -i "'.$file_path.'" -c copy -bsf:a aac_adtstoasc "D:/M3U8_OUTPUT/'.$fileName.'.mp4"';
            $command .= "\n";
            $command .="pause";



            $filePath = "{$this->parentPath}\\M3U8_BATCH\\{$fileName}.bat"; 
            File::put($filePath, $command); 


           

            // $commandF .= "$command \n";

            // dd($command);
            // // $command = 'ffmpeg -protocol_whitelist "file,http,https,tcp,tls,crypto" -i "D:/SAMPLE_M3U8/Huling selfie bago maging Single Mom – Rapbeh Pinayflix TV Free Pinay Porn.m3u8" -c copy -bsf:a aac_adtstoasc "D:/SAMPLE_M3U8/output.mp4" ';

            // // dd($command);

            // // dd($command);

            // $output  = exec($command);
            // nl2br($output);
            // dd($segmentFiles);
        }
        // $commandF .= " pause";
        // dd($commandF);
        return $commandF;

        
        // dd($m3u8Files);

        return $m3u8Files;
    }
    public function delete(){
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "0");
        $directory = $this->parentPath;
        $m3u8Files = [];
  

        if (File::exists($directory)) {
        // Get all files in the directory with .m3u8 extension
            $files = File::files($directory);

            foreach ($files as $file) {
                if ($file->getExtension() === 'm3u8') {
                    $m3u8Files[] = $file->getFilename();
                }
            }
        } else {

            // throw new Exception("Directory does not exist: $directory");
        }

        $withOutput = array();
        foreach($m3u8Files as $file){
            $fileName = str_replace("–","-",str_replace(".m3u8","",$file));

            $OutputFile = "D:\M3U8_OUTPUT\\{$fileName}.mp4";

            if (File::exists($OutputFile)) {
                $withOutput[]=$file;
            }
        }





        $withoutOutput = array_values(array_diff($m3u8Files, $withOutput));

        $withDash = array();
        foreach($withOutput as $file){
            $deletePathM3u8 = "$this->parentPath\\{$file}";
            $deletePathM3u8Content = "$this->parentPath\\{$file}_contents";

            if (File::exists($deletePathM3u8)) {
                File::delete($deletePathM3u8);
            }
            if (File::exists($deletePathM3u8Content)) {
                File::deleteDirectory($deletePathM3u8Content);
            }
           
        }

        dd("SUCCESS");




        dd($withDash);
        dd($withoutOutput);
    }
}
