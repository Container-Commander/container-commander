<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Process;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateContainer extends Component
{

    #[Validate('required')]
    public $organization;


    #[Validate('required|int')]
    public $port;

    #[Validate('nullable')]
    public $tag;

    #[Validate('nullable')]
    public $volume;

    #[Validate('nullable')]
    public $root_password;

    public function render()
    {
        return view('livewire.create-container');
    }

    public function create(){
        $validatedData = $this->validate();
        dd($validatedData);
    }

    public function updatingVolume($value){
        if($value){
            $result = Process::run("docker ps -a --filter volume={$value} --format 'table {{.ID}}|{{.Names}}|{{.Status}}|{{.Ports}}'");

            $lines = explode("\n", $result->output());
            // Initialize an empty array to hold the result
            $resultArray = [];
            // Iterate through each line
            foreach ($lines as $line) {
                // Remove leading/trailing whitespace and split by pipe character
                $values = explode('|', trim($line));
                // Add the values to the result array
                $resultArray[] = $values;
            }
            if(isset($resultArray[2])){
                $this->addError('volume', "Volume {$value} is already in use. Please select a different volume.");
            }
        }
    }
}
