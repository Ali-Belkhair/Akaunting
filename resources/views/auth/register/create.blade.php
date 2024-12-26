<x-layouts.auth>
    <x-slot name="title">
        {{ trans('auth.register_user') }}
    </x-slot>

    <x-slot name="content">
        <div>
            <img src="{{ asset('public/img/akaunting-logo-green.svg') }}" class="w-16" alt="Akaunting" />

            <h1 class="text-lg my-3">
                {{ trans('auth.register_user') }}
            </h1> 
        </div>

        <div :class="(form.response.success) ? 'w-full bg-green-100 text-green-600 p-3 rounded-sm font-semibold text-xs' : 'hidden'"
            v-if="form.response.success"
            v-html="form.response.message"
            v-cloak
        ></div>

        <div :class="(form.response.error) ? 'w-full bg-red-100 text-red-600 p-3 rounded-sm font-semibold text-xs' : 'hidden'"
            v-if="form.response.error"
            v-html="form.response.message"
            v-cloak
        ></div>

        <x-form id="auth" route="register.store" method="POST" >
            @csrf
            <div class="grid sm:grid-cols-6 gap-x-8 gap-y-6 my-3.5 lg:h-120">
                
                <x-form.group.text name="company_name" label="Company Name" form-group-class="sm:col-span-6" />
                
                {{-- <x-form.group.text name="company_email" label="Company Email"  form-group-class="sm:col-span-6" /> --}}
                
                <x-form.group.text name="email" label="Admin Email"  form-group-class="sm:col-span-6" />

                <x-form.group.password name="user_password" label="Admin password" form-group-class="sm:col-span-6" />
                
                <x-button
                    type="submit"
                    ::disabled="form.loading"
                    class="relative flex items-center justify-center bg-green hover:bg-green-700 text-white px-6 py-1.5 text-base rounded-lg disabled:bg-green-100 sm:col-span-6"
                    override="class"
                    data-loading-text="{{ trans('general.loading') }}" >
                    <span :class="[{'opacity-0': form.loading}]">{{ trans('auth.register') }}</span>
                </x-button>

            </div>
        </x-form>

        
    </x-slot>

    <x-script folder="auth" file="common" />
</x-layouts.auth>
