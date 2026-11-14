<form role="search" method="get" class="sform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr" for="s-<?php echo esc_attr( wp_unique_id() ); ?>">Search PICKORA</label>
	<input type="search" name="s" placeholder="Search reviews, guides and products" value="<?php echo esc_attr( get_search_query() ); ?>">
	<button class="btn btn--gold" type="submit">Search</button>
</form>
