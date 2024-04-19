<?php

namespace PixelParfait\LaravelAdminCommands\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

use function Laravel\Prompts\text;
use PixelParfait\LaravelAdminCommands\Commands\Traits\InteractsWithStubs;

class CreateResource extends Command
{
    use InteractsWithStubs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-resource {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create view for the resource';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = (string) str($this->argument('name') ?? text(
            label: 'Name of the model',
            placeholder: 'Post',
            required: true,
        ))
            ->studly()
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $pluralName = Str::plural($model);

        $pluralLabel = text(
            label: 'Plural name',
            default: Str::plural($model),
            required: true,
        );

        $singularLabel = text(
            label: 'Singular name',
            default: Str::singular($pluralLabel),
            required: true,
        );

        $newLabel = text(
            label: '"Add new" label',
            default: 'New '.mb_strtolower($model),
            required: true,
        );

        $thisLabel = text(
            label: '"This model" label',
            default: 'This '.mb_strtolower($model),
            required: true,
        );

        // @TODO: ask if the model has soft deletes

        $this->copyStubToApp('pages/Index', base_path("resources/js/Admin/Pages/{$pluralName}/Index.vue"), [
            'ADD_NEW_LABEL' => $newLabel,
            'ROUTE_NAME' => strtolower($pluralName),
            'PLURAL_LABEL' => $pluralLabel,
            'SINGULAR_NAME' => strtolower($model),
            'MODEL_NAME' => $model,
        ]);

        $this->copyStubToApp('pages/Create', base_path("resources/js/Admin/Pages/{$pluralName}/Create.vue"), [
            'ADD_NEW_LABEL' => $newLabel,
            'ROUTE_NAME' => strtolower($pluralName),
            'PLURAL_LABEL' => $pluralLabel,
            'SINGULAR_NAME' => strtolower($model),
            'MODEL_NAME' => $model,
        ]);

        $this->copyStubToApp('pages/Edit', base_path("resources/js/Admin/Pages/{$pluralName}/Edit.vue"), [
            'ROUTE_NAME' => strtolower($pluralName),
            'PLURAL_LABEL' => $pluralLabel,
            'SINGULAR_LABEL' => mb_strtolower($singularLabel),
            'THIS_LABEL' => mb_strtolower($thisLabel),
            'SINGULAR_NAME' => strtolower($model),
            'MODEL_NAME' => $model,
        ]);

        $this->copyStubToApp('pages/Form', base_path("resources/js/Admin/Pages/{$pluralName}/Form.vue"));
    }
}
