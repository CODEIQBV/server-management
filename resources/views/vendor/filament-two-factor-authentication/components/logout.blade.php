<div class="flex justify-center w-full">
    <form method="POST" action="{{ filament()->getCurrentPanel()->getLogoutUrl() }}">
        @csrf
        <x-filament::link tag="button" type="submit" weight="semibold">
            {{__('Logout')}}
        </x-filament::link>
    </form>
</div>