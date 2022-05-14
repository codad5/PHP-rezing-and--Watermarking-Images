<?php
namespace codad5;

class imageUpload {
	protected $files;
	protected $filedir = 'uploads/';
	protected $allowFiles = ['jpeg', 'png', 'gif', 'webp'];
	public function __construct(Array $files)
	{
		$this->files = $files;
		$this->checkImages();
	}

	protected function checkImages(){
		foreach($this->files as $key =>$value){
			foreach ($this->files[$key] as $item => $name) {
		        echo "me";		# code...
                var_dump($this->files[$key]);
				$file = $this->files[$key];

				// if ($file['error']) {
				// 	# code...
				// 	throw new \Exception("Error in file ".$file['name']);
					
				// }
				// if (!in_array($file['extension'], $this->allowFiles)) {
				// 	# code...
				// 	throw new \Exception("Invalid file type for".$file['name']);
					
				// }
				// if()
			}
		}
	}
}