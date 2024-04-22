<?php

namespace PixelParfait\LaravelAdminCommands\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use PixelParfait\LaravelAdminCommands\Commands\Traits\InteractsWithStubs;

class CreateController extends Command
{
    use InteractsWithStubs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:controller {model?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a controller for the resource';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = (string) str($this->argument('model') ?? text(
            label: 'Nom du modèle',
            placeholder: 'Post',
            required: true,
        ))
            ->studly()
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $singularName = text(
            label: 'Nom au singulier (nom de la variable)',
            default: strtolower(Str::singular($model)),
        );

        $pluralName = text(
            label: 'Nom de la ressource (préfixe des routes)',
            default: strtolower(Str::plural($model)),
        );

        $pagesDirectory = text(
            label: 'Répertoire des pages',
            default: Str::studly($pluralName),
        );

        $singularLabel = mb_strtolower(text(
            label: 'Libellé au singulier',
            placeholder: $model,
        ) ?: $model);

        $genre = select(
            label: 'Genre',
            options: ['Masculin', 'Féminin'],
            default: 'Masculin',
        );

        switch ($genre) {
            case 'Masculin':
                $defaultCreatedMessage = "Le {$singularLabel} a été créé.";
                $defaultUpdatedMessage = "Le {$singularLabel} a été mis à jour.";
                $defaultDeletedMessage = "Le {$singularLabel} a été supprimé définitivement.";
                $defaultTrashedMessage = "Le {$singularLabel} a été mis à la corbeille.";
                $defaultRestoredMessage = "Le {$singularLabel} a été restauré.";
                break;
            default:
                $defaultCreatedMessage = "La {$singularLabel} a été créée.";
                $defaultUpdatedMessage = "La {$singularLabel} a été mise à jour.";
                $defaultDeletedMessage = "La {$singularLabel} a été supprimée définitivement.";
                $defaultTrashedMessage = "La {$singularLabel} a été mise à la corbeille.";
                $defaultRestoredMessage = "La {$singularLabel} a été restaurée.";
        }

        $hasSoftDeletes = confirm(
            label: 'Le modèle implémente-t-il SoftDeletes ?',
            yes: 'Oui',
            no: 'Non',
            default: false
        );

        $createdMessage = text(
            label: 'Message de confirmation de création',
            placeholder: $defaultCreatedMessage,
        ) ?: $defaultCreatedMessage;

        $updatedMessage = text(
            label: 'Message de confirmation de mise à jour',
            placeholder: $defaultUpdatedMessage,
        ) ?: $defaultUpdatedMessage;

        $deletedMessage = text(
            label: 'Message de confirmation de suppression',
            placeholder: $defaultDeletedMessage,
        ) ?: $defaultDeletedMessage;

        if ($hasSoftDeletes) {
            $trashedMessage = text(
                label: 'Message de confirmation de mise à la corbeille',
                placeholder: $defaultTrashedMessage,
            ) ?: $defaultTrashedMessage;

            $restoredMessage = text(
                label: 'Message de confirmation de restauration',
                placeholder: $defaultRestoredMessage,
            ) ?: $defaultRestoredMessage;
        }

        if ($hasSoftDeletes) {
            $stub = 'ControllerWithSoftDeletes';
        } else {
            $stub = 'Controller';
        }

        $this->copyStubToApp($stub, base_path("app/Http/Controllers/Admin/{$model}Controller.php"), [
            'MODEL_NAME' => $model,
            'SINGULAR_RESOURCE_NAME' => $singularName,
            'PLURAL_RESOURCE_NAME' => $pluralName,
            'PAGES_DIRECTORY' => $pagesDirectory,
            'CREATED_MESSAGE' => $createdMessage,
            'UPDATED_MESSAGE' => $updatedMessage,
            'DELETED_MESSAGE' => $deletedMessage,
            'TRASHED_MESSAGE' => $trashedMessage ?? null,
            'RESTORED_MESSAGE' => $restoredMessage ?? null,
        ]);
    }
}
