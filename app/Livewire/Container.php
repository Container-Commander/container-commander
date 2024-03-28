<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Process;
use Livewire\Component;

class Container extends Component
{
    public $containers;
    public function mount(){
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
        $process = "docker start $containerId";
        $result = Process::run($process);
        if($result->successful()){
            session()->flash('success', 'Container started successfully');
        } else {
            session()->flash('fail', 'Someting went wrong, please try again');
        }
        return redirect()->route('home');
    }

    public function stopContainer(string $containerId){
        $process = "docker stop $containerId";
        $result = Process::run($process);
        if($result->successful()){
            session()->flash('success', 'Container Stopped successfully');
        } else {
            session()->flash('fail', 'Someting went wrong, please try again');
        }
        return redirect()->route('home');
    }
}
