<div>
    <input 
        type="text" 
        value="{{ $password }}" 
        id="password-field" 
        class="w-full px-4 py-2 border rounded-lg" 
        readonly
        x-data
        x-init="
            $el.select();
            document.execCommand('copy');
            $dispatch('notify', { 
                message: 'Password copied to clipboard',
                status: 'success'
            })
        "
    >
    <p class="mt-2 text-sm text-gray-600">The password has been copied to your clipboard.</p>
</div> 