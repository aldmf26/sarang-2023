<x-theme.app title="{{ $title }}" table="Y" sizeCard="11">
    <x-slot name="cardHeader">
        
    </x-slot>
    <x-slot name="cardBody">
        <form action="https://account.accurate.id/oauth/authorize" method="GET">
            <input type="hidden" name="client_id" value="3393428a-0c96-4d2c-954a-608906cb2bc0">
            <input type="hidden" name="response_type" value="code">
            <input type="hidden" name="redirect_uri" value="https://example.com/aol-oauth-callback">
            <input type="hidden" name="scope" value="item_view item_save sales_invoice_view">
            <button type="submit">Login dengan Accurate Online</button>
        </form>
      
    </x-slot>

</x-theme.app>
