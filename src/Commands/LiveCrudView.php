<?php

namespace Imritesh\LiveCrud\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LiveCrudView extends GeneratorCommand
{
    protected $signature = 'crud:view {name}';

    protected $description = 'Generate View For Crud Command';


    protected $emailNames = [
        'email',
        'email_address',
    ];

    public function handle()
    {
        if (!is_dir(resource_path('views/livewire'))) {
            mkdir(resource_path('views/livewire'));
        }
        $viewPath = resource_path('views/livewire/') . strtolower($this->arguments()['name']) . '.blade.php';
        $content = file_get_contents($this->getStub());
        $content = $this->buildContent($content);
        file_put_contents($viewPath, $content);

        $this->info('View Generated');
    }

    public function buildContent($content)
    {
        $array = [
            '{{ title }}' => ucfirst($this->arguments()['name']),
            '{{ headings }}' => $this->getHeadings(),
            '{{ renderedData }}' => $this->getRenderedData(),
            '{{ form }}' => $this->getForm()
        ];

        return str_replace(array_keys($array), array_values($array), $content);
    }

    public function getForm()
    {
        $class = 'App\\Models\\' . $this->arguments()['name'];
        $model = new $class;
        $columns = $model->getFillable();
        $str = '';
        $c = 1;
        foreach ($columns as $column) {
            if ($column != 'created_at' || $column != 'updated_at') {
                if ($c == 1) {
                    $str .= $this->makeInput($column) . PHP_EOL;
                } else {
                    $str .= $this->makeInput($column) . PHP_EOL;
                }
            }
            $c++;
        };
        return $str;
    }

    public function makeInput($name)
    {
        $type = $this->getType($name);
        $label = ucfirst(str_replace('-', ' ', Str::slug($name)));
        $message = '{{ $message }}';
        if (config('livecrud.template') == 'tailwind') {
            return "<div><label class='block'><span class='text-gray-700 @error('{$name}') text-red-500  @enderror'>{$label}</span><input type='{$type}' class='mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('{$name}')  border-red-500 @enderror focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50' wire:model='{$name}'>@error('{$name}')<span class='text-red-500 text-sm'>{$message}</span>@enderror</label></div>";
        }
        if (config('livecrud.template') == 'bootstrap') {
            return "<div class='form-group'><label for='{$name}'>{$label}</label><input type='{$type}' class='form-control @error('{$name}')  is-invalid @enderror' wire:model='{$name}'>@error('{$name}')<div class='invalid-feedback'>{$message}</div>@enderror</div>";

        }


    }

    public function getType($name)
    {
        if (in_array(strtolower($name), $this->emailNames)) {
            return 'email';
        }
        if (strtolower($name) == 'password') {
            return 'password';
        }
        return 'text';
    }

    public function getRenderedData()
    {
        $class = 'App\\Models\\' . $this->arguments()['name'];
        $model = new $class;
        $columns = $model->getFillable();
        $str = '';
        $c = 1;
        $str .= '@foreach($rows as $row)' . PHP_EOL;
        $str .= '<tr>';
        if (config('livecrud.template') == 'tailwind') {
            foreach ($columns as $column) {
                if ($column != 'created_at' || $column != 'updated_at') {
                    if ($c == 1) {
                        $str .= $this->getDynamicData(str_replace('-', ' ', Str::slug($column))) . PHP_EOL;
                    } else {
                        $str .= $this->getDynamicData(str_replace('-', ' ', Str::slug($column))) . PHP_EOL;
                    }
                }
                $c++;
            }
            $str .= '<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900" wire:click="edit({{ $row->id }})">{{ __("Edit") }}</a>
                                <a href="#" class="text-indigo-600 hover:text-indigo-900" wire:click="confirmDelete({{ $row->id }})">{{ __("Delete") }}</a>
                            </td></tr>';
            $str .= '@endforeach' . PHP_EOL;

        }
        if (config('livecrud.template') == 'bootstrap') {
            foreach ($columns as $column) {
                if ($column != 'created_at' || $column != 'updated_at') {
                    if ($c == 1) {
                        $str .= $this->getDynamicData(str_replace('-', ' ', Str::slug($column))) . PHP_EOL;
                    } else {
                        $str .= $this->getDynamicData(str_replace('-', ' ', Str::slug($column))) . PHP_EOL;
                    }
                }
                $c++;
            }
            $str .= '<td>
                                <a href="#" class="text-primary" wire:click.prevent="edit({{ $row->id }})">
<svg xmlns="http://www.w3.org/2000/svg" style="width:20px; height: 20px;" viewBox="0 0 20 20" fill="currentColor">
  <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
  <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
</svg>
</a>
                                <a href="#" class="text-danger" wire:click.prevent="confirmDelete({{ $row->id }})"> <svg xmlns="http://www.w3.org/2000/svg" style="width:20px; height: 20px;" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg></a>
                            </td></tr>';
            $str .= '@endforeach' . PHP_EOL;

        }
        return $str;
    }

    public function getDynamicData($name): string
    {
        if (config('livecrud.template') == 'bootstrap') {
            return ' <td>{{ $row->' . $name . '}}</td>' . PHP_EOL;
        }
        return ' <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row->' . $name . '}}</td>' . PHP_EOL;
    }

    public function getHeadings(): string
    {
        $class = 'App\\Models\\' . $this->arguments()['name'];
        $model = new $class;
        $columns = $model->getFillable();
        $c = 1;
        $str = '';
        foreach ($columns as $column) {
            if ($column != 'created_at' || $column != 'updated_at') {
                if ($c == 1) {
                    $str .= $this->getInput(str_replace('-', ' ', Str::slug($column))) . PHP_EOL;
                } else {
                    $str .= $this->getInput(str_replace('-', ' ', Str::slug($column))) . PHP_EOL;
                }
            }
            $c++;
        }

        return $str;
    }

    public function getInput($name): string
    {
        if (config('livecrud.template') == 'bootstrap') {
            return '<th>' . strtoupper($name) . '
                            </th>' . PHP_EOL;
        }
        return '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ' . strtoupper($name) . '
                            </th>' . PHP_EOL;
    }


    /**
     * Get the stub file for the generator.
     *
     * @return string
     * @throws \Exception
     * @throws \Exception
     */
    protected function getStub()
    {
        if (config('livecrud.template') == 'bootstrap') {
            if (file_exists(base_path() . '/stubs/bootstrap.view.php.stub')) {
                return base_path() . '/stubs/bootstrap.view.php.stub';
            }
            return base_path() . '/vendor/imritesh/livecrud/src/stubs/bootstrap.view.php.stub';
        }

        if (file_exists(base_path() . '/stubs/view.php.stub')) {
            return base_path() . '/stubs/view.php.stub';
        }
        return base_path() . '/vendor/imritesh/livecrud/src/stubs/view.php.stub';
    }

}
