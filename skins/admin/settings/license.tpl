{assign var=l value=$license.response}

{capture name=section}
<p>The license server has returned following information:</p>
<div class="box">
<table class="table table-striped">
<tr><th>Parameter</th><th>Value</th></tr>
<tr class='{cycle values="cycle,"}'><td>License</td><td class='{if $l.status eq -2}red{/if}'>{$l.license}</td></tr>
<tr class='{cycle values="cycle,"}'><td>Company</td><td>{$l.company}</td></tr>
<tr class='{cycle values="cycle,"}'><td>Email</td><td>{$l.email}</td></tr>
<tr class='{cycle values="cycle,"}'><td>Domain</td><td class='{if $l.status eq 2}red{/if}'>{$l.domain}</td></tr>
<tr class='{cycle values="cycle,"}'><td>Expiration</td><td class='{if $l.status eq 4}red{/if}'>{$l.expired}</td></tr>
<tr class='{cycle values="cycle,"}'><td>Status</td><td class='{if $l.status neq 1}red{/if}'>[{$l.status}] {$l.status_note}</td></tr>
</table>
{if !$l.can_be_registered}
<p>Please contact CartWorks support team support@cartworks.com to change registration data</p>
{else}
<p>Please contact CartWorks support team support@cartworks.com for details</p>
{/if}
</div>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title="`$lng.lbl_license` $license_code"}

{if $l.can_be_registered}
{capture name=section}
<form action='index.php?target=license&mode=register' method='POST' id='regLicenseForm'>
<table class="header">
    <tr>
        <td>Company</th>
        <td><input type='text' name='reg[company]' class="required" /></td>
    </tr>
    <tr>
        <td>Email</th>
        <td><input type='text' name='reg[email]' class="required email" name='email' id='email'/></td>
    </tr>
    <tr>
        <td>Retype email</th>
        <td><input type='text' name='reg[email2]' class="required email" equalTo='#email' name='email_retype'  id='email_retype' onpaste='return no_paste_email()' onDrop="return no_paste_email()" autocomplete=off /></td>
    </tr>
    <tr>
        <td>Domain</th>
        <td><input type='text' name='reg[domain]' class="required" /></td>
    </tr>
</table>
<p>Please fill all fields carefully with correct info.</p>

<input type='submit' value='Register' />
</form>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title="`$lng.lbl_license` $license_code"}

<script type="text/javascript">
{literal}
    function no_paste_email() {
        alert("Please retype email to avoid mistakes");
        return false;
    }

  $(document).ready(function(){
    regLicenseForm = $("#regLicenseForm");
    regLicenseForm.validate({
      rules: {
        email: "required",
        email_retype: {
          equalTo: "#email"
        }
      }
    });
  });
{/literal}
</script>
{/if}
