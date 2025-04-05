<x-nav-link :href="route('admin.messages.index')" :active="request()->routeIs('admin.messages.*')">
    <i class="fas fa-comment-alt mr-2"></i>
    {{ __('Messages') }}
</x-nav-link> 