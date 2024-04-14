<?php

namespace App\Livewire;

use App\Services\MySql;
use App\Shell\Docker;
use App\Shell\DockerNetworking;
use App\Shell\DockerTags;
use Illuminate\Support\Facades\Process;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Native\Laravel\Facades\Notification;

class CreateContainer extends Component
{

    protected $dockerTagsClass = DockerTags::class;

    #[Validate('required')]
    public $container_name;

    #[Validate('required')]
    public $alias;

    #[Validate('required|string')]
    public $organization ;

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

    public function shortName(): string
    {
        return strtolower(class_basename(MySql::class));
    }

    protected function containerName(): string
    {
        $this->port = $this->port ?? 3306;
        return 'TO--' . $this->shortName() . '--' . $this->tag . "--{$this->port}";
    }

    protected function shortNameWithVersion(): string
    {
        // Check if tag represents semantic version (v5.6.0, 5.7.4, or 8.0) and return major.minor
        // (eg mysql5.7) or return the actual tag prefixed by a dash (eg redis-buster)
        if (! preg_match('/v?(0|(?:[1-9]\d*))(?:\.(0|(?:[1-9]\d*))(?:\.(0|(?:[1-9]\d*)))?)/', $this->tag)) {
            return $this->shortName() . "-{$this->tag}";
        }

        $version = trim($this->tag, 'v');
        [$major, $minor] = explode('.', $version);

        return $this->shortName() . "{$major}.{$minor}";
    }

    protected function buildParameters(): void
    {
        $this->tag = $this->resolveTag($this->tag); // Overwrite "latest" with actual latest tag
        $this->container_name = $this->containerName();
        $this->alias = $this->shortNameWithVersion();
        $this->volume = $this->volume ?? "mysql_data"; // TODO: make it dynamic
    }

    protected function resolveTag($responseTag): string
    {
        if(empty($responseTag)){
            $responseTag = 'latest';
        }
        return app()->make($this->dockerTagsClass)->resolveTag($responseTag);
    }

    public function create(){
        $this->organization = 'mysql';
        $this->buildParameters();
        $validatedData = $this->validate();
        $validatedData['image_name'] = 'mysql-server';
        $validatedData['allow_empty_password'] = 1;
        $validatedData['root_password'] = $validatedData['root_password'] ?? '';
        $container = (new Docker)->bootContainer((new MySql)->dockerRunTemplate, $validatedData);
        if($container){
            session()->flash('success', 'Container started successfully');
        } else {
            session()->flash('fail', 'Someting went wrong, please try again');
        }
        return redirect()->route('home');
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
