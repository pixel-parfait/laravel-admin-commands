<?php

namespace PixelParfait\LaravelAdminCommands\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use PixelParfait\LaravelAdminCommands\Commands\Traits\InteractsWithStubs;

class CreatePages extends Command
{
    use InteractsWithStubs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-pages {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create views for the resource';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = (string) str($this->argument('name') ?? text(
            label: 'Nom du modèle',
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
            label: 'Libellé au pluriel',
            placeholder: Str::plural($model),
        ) ?: Str::plural($model);

        $singularLabel = text(
            label: 'Libellé au singulier',
            placeholder: Str::singular($pluralLabel),
        ) ?: Str::singular($pluralLabel);

        $genre = select(
            label: 'Genre',
            options: ['Masculin', 'Féminin'],
            default: 'Masculin',
        );

        $defaultNewLabel = ($genre == 'Masculin' ? 'Nouveau' : 'Nouvelle').' '.mb_strtolower($singularLabel);
        $defaultThisLabel = ($genre == 'Masculin' ? 'Ce' : 'Cette').' '.mb_strtolower($singularLabel);

        $newLabel = text(
            label: 'Libellé de "Nouveau modèle"',
            placeholder: $defaultNewLabel,
        ) ?: $defaultNewLabel;

        $thisLabel = text(
            label: 'Libellé de "Ce modèle"',
            placeholder: $defaultThisLabel,
        ) ?: $defaultThisLabel;

        $hasSoftDeletes = confirm(
            label: 'Le modèle implémente-t-il SoftDeletes ?',
            yes: 'Oui',
            no: 'Non',
            default: false
        );

        if ($hasSoftDeletes) {
            $path = 'pages/soft-deletes';
        } else {
            $path = 'pages';
        }

        $this->copyStubToApp("{$path}/Index", base_path("resources/js/Admin/Pages/{$pluralName}/Index.vue"), [
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

        $this->copyStubToApp("{$path}/Edit", base_path("resources/js/Admin/Pages/{$pluralName}/Edit.vue"), [
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
