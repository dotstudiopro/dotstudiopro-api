<div class='container'>
   <h2>dotstudioPRO API Plugin Options</h2>
   <div>
      <form action='' method='POST' enctype='multipart/form-data'>
         <table class='form-table widefat'>
            <thead>
            </thead>
            <tbody>
               <tr>
                  <td>dotstudioPRO Account Dashboard<br/><span class='description'>Opens in a new window</span></td>
                  <td><a class="button" href="https://www.dotstudiopro.com/user/login" target="_blank">LOGIN</a></td>
               </tr>
               <tr>
                  <td>dotstudioPRO API Key<br/><span class='description'>Don't have an API Key? <a href="https://beta.dotstudiopro.com/user/register" target="_blank">Click Here.</a></span></td>
                  <td><input type='text' name='dspdev_api_key' value='<?php echo get_option('dspdev_api_key') ?>' /></td>
               </tr>
               <tr>
                  <td colspan=2><b>Development Options</b><br/><span class='description'>Please note: any options set here will override normal settings.  Please make sure to turn these settings off when you are done testing.</span></td>
               </tr>
               <tr>
                  <td>Development Mode</td>
                  <td><input type='checkbox' name='dspdev_api_development_check' value='1' <?php echo get_option("dspdev_api_development_check") == 1 ? 'checked="checked"' : '' ?> /></td>
               </tr>
               <tr>
                  <td>Development Country (Abbreviation)</td>
                  <td><input type='text' name='dspdev_api_development_country' value='<?php echo get_option("dspdev_api_development_country") ?>' /></td>
               </tr>
               <tr>
                  <td>Reset Token on Save<br><span class='description'>Use in case you believe you have issues with token authentication.</span></td>
                  <td><input type='checkbox' name='dspdev_api_token_reset' value='1' <?php echo get_option("dspdev_api_token_reset") == 1 ? 'checked="checked"' : '' ?> /></td>
               </tr>
               <input type='hidden' name='dspdev-api-save-admin-options' value='1' />
            </tbody>
            <tfoot>
               <tr>
                  <td colspan=2><button class='button button-primary'>Save</button></td>
               </tr>
            </tfoot>
         </table>
      </form>
   </div>
</div>