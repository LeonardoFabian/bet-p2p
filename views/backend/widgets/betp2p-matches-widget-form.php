<p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
        <?php esc_html_e( 'Title', 'betp2p' ); ?>:
    </label>
    <input 
        class="widefat" 
        id="<?php echo $this->get_field_id( 'title' ); ?>" 
        name="<?php echo $this->get_field_name( 'title' ); ?>"
        type="text"
        value="<?php echo $title; ?>"
    />
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'number' ); ?>">
        <?php esc_html_e( 'Number of matches to show', 'betp2p' ); ?>:
    </label>
    <input 
        class="widefat" 
        id="<?php echo $this->get_field_id( 'number' ); ?>" 
        name="<?php echo $this->get_field_name( 'number' ); ?>"
        type="number"
        step="1"
        min="1"
        size="3"
        value="<?php echo $number; ?>"
    />
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'sport_key' ); ?>">
        <?php esc_html_e( 'Sports to show', 'betp2p' ); ?>:
    </label>
    <select 
        class='widefat' 
        id="<?php echo $this->get_field_id( 'sport_key' ); ?>"
        name="<?php echo $this->get_field_name( 'sport_key' ); ?>" type="text"
    >
        <option value='all'<?php echo ( $sports == 'all' ) ? 'selected' : ''; ?>>
            <?php _e( 'All' ); ?>
        </option>

        <?php foreach( $sports_list as $sport_item ) : ?>

            <?php
                $sport_item_key = strtolower( $sport_item->{'meta_value'} );
            ?> 

            <option value='<?php esc_html_e( $sport_item_key ) ?>'<?php echo ( $sports == $sport_item_key ) ? 'selected' : ''; ?>>
                <?php esc_html_e( $sport_item->{'meta_value'} ); ?>
            </option>
        <?php endforeach; ?>
    </select>     
</p>

<p>    
    <input 
        class="checkbox" 
        id="<?php echo $this->get_field_id( 'show_date' ); ?>" 
        name="<?php echo $this->get_field_name( 'show_date' ); ?>"
        type="checkbox"
        <?php
            checked( $show_date );
        ?>
    />
    <label for="<?php echo $this->get_field_id( 'show_date' ); ?>">
        <?php esc_html_e( 'Display matches date?', 'betp2p' ); ?>
    </label>
</p>