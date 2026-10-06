@props(['action', 'confirm' => 'Tem certeza que deseja excluir?'])

<form method="POST" action="{{ $action }}" data-confirm="{{ $confirm }}" class="inline">
    @csrf
    @method('DELETE')
    <x-button variant="ghost" {{ $attributes }}>{{ $slot->isEmpty() ? 'Excluir' : $slot }}</x-button>
</form>
