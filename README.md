# Dandy Blocks
A collection of Gutenberg blocks built on top of ACF.

## Registering a new block

### config.json

Blocks need to be added to the `config.json` file.

Inside of the `config.json` there is a `blocks` object. Object keys are the block slugs used for loading the template. The values of the object should match the options available to [acf_register_block_type](https://www.advancedcustomfields.com/resources/acf_register_block_type/).

The basic structure should look like:

```json
{
  "blocks": {
    "blockSlug": {
      //... block options
    }
  }
}
```

Some block options are automatically configured and some can be overridden. Look at the `acf_register_block_type` section of `dandy-blocks.php` to see how the block is setup in code.

### Templates

Place the block template in the `blocks/<blockSlug>/block.php` file. You may place css/js in the `blocks/<blockSlug>` directory/

Templates should use default clientkit classnames when possible and included styles should expect to be processed by CK (meaning you can use color variables and the like).

### Styles & Javascript

No styles or javascript are loaded by default. It is up to the theme to import the styles and javascript. If you add styles or js to a block, make sure it gets imported to the root `styles.css` or `scripts.js`

Block styles should be prefixed with `dandy-block__` to make sure there's no conflicts with existing blocks WordPress might have or other plugins register.
