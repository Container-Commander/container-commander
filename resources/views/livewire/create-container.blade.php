<div>
	<form class="w-full max-w-lg m-[auto]" wire:submit.prevent="create">

		<div class="w-full">
			<div>
				@php $databases = ['mysql']; @endphp
				<x-form.label value="Select Container" required="true" />
				<x-form.select :listings="$databases" required wire:model="organization" />
				@error('organization') <span class="text-red-600">{{ $message }}</span> @enderror
			</div>

			<div>
				<x-form.label value="Port" required="true" class="mt-3" />
				<x-form.input required="true" placeholder="3306" wire:model="port" name="port" />
				@error('port') <span class="error">{{ $message }}</span> @enderror
			</div>

			<div>
				<x-form.label value="Tag" required="false" class="mt-3" />
				<x-form.input placeholder="Leave Empty for latest" wire:model="tag" />
				@error('tag') <span class="error">{{ $message }}</span> @enderror
			</div>

			<div>
				<x-form.label value="Volume" required="false" class="mt-3" />
				<x-form.input placeholder="Leave Empty for default" wire:model.blur="volume" />
				@error('volume') <span class="text-red-600">{{ $message }}</span> @enderror
			</div>

			<div>
				<x-form.label value="Root Password" required="false" class="mt-3" />
				<x-form.input placeholder="Leave Empty for null" wire:model="root_password" />
				@error('root_password') <span class="error">{{ $message }}</span> @enderror
			</div>

			<input type="submit" class="bg-gray-50 hover:bg-gray-300 text-[#09caeb] font-semibold py-2 px-4 border hover:border-transparent rounded mt-3 float-right cursor-pointer">
		</div>
	</form>
</div>