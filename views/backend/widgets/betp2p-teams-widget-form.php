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
    <input 
        class="checkbox" 
        id="<?php echo $this->get_field_id( 'show_logo' ); ?>" 
        name="<?php echo $this->get_field_name( 'show_logo' ); ?>"
        type="checkbox"
        <?php
            checked( $show_logo );
        ?>
    />
    <label for="<?php echo $this->get_field_id( 'show_logo' ); ?>">
        <?php esc_html_e( 'Display team logo?', 'betp2p' ); ?>
    </label>
</p>