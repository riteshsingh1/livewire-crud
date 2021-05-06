<?php
namespace Imritesh\LiveCrud\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Crud extends GeneratorCommand
{
    protected $signature = 'crud:make {name}';

    protected $description = 'Generate Crud Livewire';
    protected $search = '$this->search';
    public function handle()
    {
        parent::handle();


        // Get the fully qualified class name (FQN)
        $class = $this->qualifyClass($this->getNameInput());

        // get the destination path, based on the default namespace
        $path = $this->getPath($class);
        $content = file_get_contents($path);
        // Update the file content with additional data (regular expressions)
        $this->info('Generating Livewire Component');

        $content = $this->buildContent($content);
        file_put_contents($path, $content);
        $this->info('Livewire Component Generated');

        $this->info('Generating View');

        Artisan::call('crud:view', ['name' => $this->arguments()['name']]);
    }

    public function buildContent($content)
    {
        $array = [
            '{{ namespace }}' => $this->getDefaultNamespace('App'),
            '{{ name }}' => ucfirst($this->arguments()['name']),
            '{{ blade }}' => Str::slug($this->arguments()['name']),
            '{{ properties }}' => $this->buildProperties(),
            '{{ useclasses }}' => $this->usedClasses(),
            '{{ setedibleval }}' => $this->setEditableValues(),
            '{{ codeTosave }}' => $this->getSaveCode(),
            '{{ rules }}' => $this->getRules(),
            '{{ query }}' => $this->search(),
            '{{ reset }}' => $this->resetForms()
        ];
        return str_replace(array_keys($array), array_values($array), $content);
    }

    public function resetForms()
    {
        $class = 'App\\Models\\' . $this->arguments()['name'];
        $model = new $class;
        $columns = $model->getFillable();
        $str = '';
        $c = 1;
        foreach ($columns as $column) {
            if ($column != 'created_at' || $column != 'updated_at') {
                if ($c == 1) {
                    $str .= '$this->'.str_replace('-', '_', Str::slug($column)) . '= "";' . PHP_EOL;
                } else {
                    $str .= '$this->'.str_replace('-', '_', Str::slug($column)) . '= "";' . PHP_EOL;
                }
            }
            $c++;
        }
        return $str;
    }

    public function search()
    {
        $class = 'App\\Models\\' . $this->arguments()['name'];
        $model = new $class;
        $columns = $model->getFillable();
        $str = '$model = Model::';
        $i = 0;
        foreach ($columns as $column) {
            if ($column != 'created_at' || $column != 'updated_at' || $column!='password') {
                if (!$i) {
                    $str .= "where('". $column . "', 'like', '%'.$this->search.'%')";
                } else {
                    $str .= "->orWhere('" . $column . "', 'like', '%'.$this->search.'%')";
                }
            }
            $i++;
        }
        $str .= '->latest()->paginate($this->paginate);';
        return $str;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Livewire';
    }

    public function getRules()
    {
        $class = 'App\\Models\\' . $this->arguments()['name'];
        $model = new $class;
        $columns = $model->getFillable();
        $str = '';
        foreach ($columns as $column) {
            if ($column != 'created_at' || $column != 'updated_at') {
                $str .= "'" . str_replace('-', '_', Str::slug($column)) . "' => 'required'," . PHP_EOL;
            }
        }
        return 'protected $rules = [' . PHP_EOL . $str . PHP_EOL . '];' . PHP_EOL;
    }

    public function buildProperties(): string
    {
        $class = 'App\\Models\\' . $this->arguments()['name'];
        $model = new $class;
        if (!class_exists($class)){
            throw new \Exception('Model Not Found. Please Check if Model Exists at -'.$class);
        }
        $columns = $model->getFillable();
        $str = '';
        $c = 1;
        foreach ($columns as $column) {
            if ($column != 'created_at' || $column != 'updated_at') {
                if ($c == 1) {
                    $str .= 'public $' . str_replace('-', '_', Str::slug($column)) . ';' . PHP_EOL;
                } else {
                    $str .= '   public $' . str_replace('-', '_', Str::slug($column)) . ';' . PHP_EOL;
                }
            }
            $c++;
        }
        return $str;
    }

    public function setEditableValues()
    {
        $class = 'App\\Models\\' . $this->arguments()['name'];
        $model = new $class;
        $columns = $model->getFillable();
        $str = '';
        $c = 1;
        foreach ($columns as $column) {
            if ($column != 'created_at' || $column != 'updated_at') {
                if ($c > 1) {
                    $str .= '$this->' . str_replace('-', '_', Str::slug($column)) . '= $model->' . $column . ';' . PHP_EOL;
                } else {
                    $str .= '$this->' . str_replace('-', '_', Str::slug($column)) . '= $model->' . $column . ';' . PHP_EOL;
                }
            }
            $c++;
        }

        return $str;
    }

    public function getSaveCode()
    {
        $class = 'App\\Models\\' . $this->arguments()['name'];
        $model = new $class;
        $columns = $model->getFillable();
        $str = '';
        $c = 1;
        foreach ($columns as $column) {
            if ($column != 'created_at' || $column != 'updated_at') {
                if ($c > 1) {
                    $str .= '$model->' . str_replace('-', '_', Str::slug($column)) . '= $this->' . str_replace('-', '_', Str::slug($column)) . ';' . PHP_EOL;
                } else {
                    $str .= '   $model->' . str_replace('-', '_', Str::slug($column)) . '= $this->' . str_replace('-', '_', Str::slug($column)) . ';' . PHP_EOL;
                }
            }
            $c++;
        }
        $str .= ' $model->save();';

        return $str;
    }

    protected function usedClasses()
    {
        return 'use Livewire\Component;' . PHP_EOL . 'use Livewire\WithPagination;' . PHP_EOL . 'use App\\Models\\' . $this->arguments()['name'] . ' as Model;' . PHP_EOL;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $this->checkIfModelExists();

        if(config('livecrud.template') == 'bootstrap')
        {
            if (file_exists(base_path() . '/stubs/bootstrap.crud.php.stub')){
                return base_path() . '/stubs/bootstrap.view.php.stub';
            }
            return base_path().'/vendor/imritesh/livecrud/src/stubs/bootstrap.crud.php.stub';
        }

        if (file_exists(base_path() . '/stubs/crud.php.stub')){
            return base_path() . '/stubs/crud.php.stub';
        }
        return base_path().'/vendor/imritesh/livecrud/src/stubs/crud.php.stub';
    }

    public function checkIfModelExists()
    {
        $class = 'App\\Models\\' . $this->arguments()['name'];
        if (!class_exists($class)){
            throw new \Exception('Model Not Found. Please Check if Model Exists at -'.$class);
        }
    }

}
