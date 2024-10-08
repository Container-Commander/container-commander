<?php

namespace App\Livewire;

use App\Shell\Docker;
use Illuminate\Support\Facades\Process;
use Livewire\Component;

class Container extends Component
{
    public $containers;

    public string $containerErrorMessage;

    public bool $isDockerRunning = true;
    public bool $isDockerInstalled = true;

    public function mount(){
        $docker = new Docker;
        if (!$docker->isInstalled()){
            $this->containerErrorMessage = "Docker is not installed on your system.";
            $this->isDockerRunning = $this->isDockerInstalled = false;
        }
        if (!$docker->isDockerServiceRunning()){
            $this->containerErrorMessage = "Docker is not started; please start Docker.";
            $this->isDockerRunning = false;
        }
        // dd($docker->isDockerServiceRunning());
        $process = sprintf(
            'docker ps -a --filter "name=TO-" --format "table %s%s"',
            '{\"container_id\": \"{{.ID}}\",\"name\":\"{{.Names}}\",\"status\":\"{{.Status}}\",\"state\":\"{{.State}}\",\"ports\":\"{{.Ports}}\",',
            '\"label\":\"{{.Label \"com.ejaaz.commander.Base_Alias\"}}{{.Label \"com.ejaaz.commander.Full_Alias\"}}\" },'
        );
        $result = Process::run($process);
        $this->containers = json_decode("[".substr($result->output(), 0,-2)."]");
    }
    public function render()
    {
        return view('livewire.container', ['containers' => $this->containers]);
    }

    public function startContainer($containerId){
        if((new Docker)->startContainer($containerId)){
            session()->flash('success', 'Container started successfully');
        } else {
            session()->flash('fail', 'Someting went wrong, please try again');
        }
        return redirect()->route('home');
    }

    public function stopContainer(string $containerId){
        if((new Docker)->stopContainer($containerId)){
            session()->flash('success', 'Container Stopped successfully');
        } else {
            session()->flash('fail', 'Someting went wrong, please try again');
        }
        return redirect()->route('home');
    }

    public function deleteContainer(string $containerId){

        if((new Docker)->removeContainer($containerId)){
            session()->flash('success', 'Container Deleted successfully');
        } else {
            session()->flash('fail', 'Someting went wrong, please try again');
        }
        return redirect()->route('home');
        
    }
}
