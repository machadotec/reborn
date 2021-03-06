<div class="form_builder bootstrap-3">
    <?php
        if (! is_null($this->legend) ) {
            echo '<h3 class="legend_headers">';
            echo $this->legend;;
            echo '</h3>';
        }
    ?>

    <div class="form_wrapper">

        <?php if(isset($this->errors['honey_pot'])) : ?>
        <span class="error honeypot-error"><?php echo $this->errors['honey_pot']; ?></span>
        <?php endif; ?>

    <?php echo $this->start; ?>

        <?php if (! empty($this->hiddens) ) : ?>

        <div class="hidden-area" style="display: none;">
        <?php foreach($this->hiddens as $name => $value) : ?>
            <?php echo \Form::hidden($name, $value); ?>
        <?php endforeach; ?>
        </div>

        <?php endif; ?>

        <?php foreach($this->fields as $name => $field) : ?>

        <?php if($this->hasPrepend($name)) : ?>
        <?php echo $this->makePrepend($name); ?>
        <?php endif; ?>

        <?php if( $field['type'] == 'checkbox' or $field['type'] == 'radio'  ) : ?>
        <div class="<?php echo $field['type']; isset($this->errors[$name]) ? ' has-error' : ''; ?>">
            <?php echo $this->labels[$name]; ?>
            <label>
                <?php echo $this->fields[$name]['html']; ?>
                <?php echo $this->fields[$name]['info']; ?>
            </label>

            <?php if(isset($this->errors[$name])) : ?>
            <p class="error field-error"><?php echo $this->errors[$name]; ?></p>
            <?php endif; ?>
        </div> <!-- end of <?php echo $field['type']; ?> -->

        <?php else : ?>
        <div class="form-group <?php echo isset($this->errors[$name]) ? ' has-error' : ''; ?>">
            <?php echo $this->labels[$name]; ?>

            <div class="form-field">
                <?php echo $this->fields[$name]['html']; ?>

                <?php if(isset($this->errors[$name])) : ?>
                <span class="error field-error"><?php echo $this->errors[$name]; ?></span>
                <?php endif; ?>

                <p class="help-block"><?php echo $this->fields[$name]['info']; ?></p>
            </div> <!-- end of form-field -->
        </div> <!-- end of form-group -->
        <?php endif; ?>

        <?php if($this->hasAppend($name)) : ?>
        <?php echo $this->makeAppend($name); ?>
        <?php endif; ?>

        <?php endforeach; ?>

        <div class="form-block form-action button-wrapper">

            <?php foreach($this->submit as $submit) : ?>
            <?php echo $submit; ?>
            <?php endforeach; ?>

            <?php echo $this->reset; ?>

            <?php echo $this->cancel; ?>

        </div>

    </form>

    </div> <!-- end of form_wrapper -->
</div> <!-- end of form_builder -->
