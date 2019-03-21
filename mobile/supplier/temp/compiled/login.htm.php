<!DOCTYPE HTML>
<html>
<head>
<?php echo $this->fetch('html_header.htm'); ?>
<script>
      function validate()
      {
        var error = '';
        var username = document.forms['theForm'].username.value;
        var password = document.forms['theForm'].password.value;
        username = $.trim(username);
        password = $.trim(password);
        if(username == '')
        {
            error += '用户名不能为空&nbsp;';
        }
        if(password == '')
        {
            error += '密码不能为空&nbsp;';
        }
        
        <?php if ($this->_var['gd_version'] > 0): ?>
        var cpacha = document.forms['theForm'].cpacha.value;
        cpacha = $.trim(cpacha);
        if(captcha == '')
        {
          error += '验证码不能为空&nbsp;';
        }
        <?php endif; ?>
        if(error.length > 0)
        {
          $.zalert.add(error,1);
          return false;
        }
        else
        {
          return true;
        }
      }
    </script>
</head>
<body>
<?php echo $this->fetch('page_header.htm'); ?>
<section>
  <form method="post" action="privilege.php" name='theForm' onsubmit="return validate()">
    <div class="login_dl">
    <table cellspacing="0" cellpadding="0" align="center" width="100%">
      <tr>
        <td class="logo_img"><img src="../supplier/images/logo.png"/></td>
      </tr>
      <tr>
        <td><div class="login_div"><label class="label_input1"></label><input type="text" name="username" class="text_input text_input1" placeholder="请输入用户名"/></div></td>
      </tr>
      <tr>
        <td><div class="login_div"><label class="label_input2"></label><input type="password" name="password"  class="text_input text_input2" placeholder="请输入密码"/></div></td>
      </tr>
      <?php if ($this->_var['gd_version'] > 0): ?>
      <tr>
        <td><input type="text" name="captcha" class="capital text_input" placeholder="验证码" style="width:90px; margin-right:5px; float:left; "/>
          <img src="index.php?act=captcha&<?php echo $this->_var['random']; ?>" width="126" height="38" alt="CAPTCHA" border="0" onclick= 'this.src="index.php?act=captcha&"+Math.random()' style="cursor: pointer; float:left " title="<?php echo $this->_var['lang']['click_for_another']; ?>" /></td>
      </tr>
      <?php endif; ?>
      <tr>
        <td align="center"><input type="submit" value="登&nbsp;录" class="button2"/></td>
      </tr>
      <tr class="low_height">
        <td><label class="bf1 login_ffri">
            <input type="checkbox" name="remember" id="remember" value="1" checked="" >
            保存登录信息</label>
          <a class="login_ffleft" href="user.php?act=get_password&step=1">找回密码</a></td>
      </tr>
    </table>
    <input type="hidden" name="act" value="signin" />
    </div>
  </form>
</section>
</body>
</html>
