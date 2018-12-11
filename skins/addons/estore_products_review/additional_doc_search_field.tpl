{if $docs_type eq 'O' && $current_target eq 'docs_O'}
        <div class="form-group">
            <label class="col-xs-12"><input type="checkbox" name="posted_data[estore][no_review]"{if $search_prefilled eq "" or $search_prefilled.estore.no_review} checked="checked"{/if} class="checkbox-middle" /> {$lng.lbl_products_no_reviews} </label>
        </div>
{/if}
