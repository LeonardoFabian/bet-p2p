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
    <label for="<?php echo $this->get_field_id( 'share_type' ); ?>">
        <?php esc_html_e( 'Share type', 'betp2p' ); ?>:
    </label>
    <select 
        class='widefat' 
        id="<?php echo $this->get_field_id( 'share_type' ); ?>"
        name="<?php echo $this->get_field_name( 'share_type' ); ?>" type="text"
    >
        <option value='open'<?php echo ( $share_type == 'open' ) ? 'selected' : ''; ?>>
            <?php _e( 'Open' ); ?>
        </option>

        <option value='closed'<?php echo ( $share_type == 'closed' ) ? 'selected' : ''; ?>>
            <?php _e( 'Closed' ); ?>
        </option>
        
    </select>     
</p>