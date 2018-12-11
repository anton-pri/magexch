<p>
    Set a post-hook for function 'dashboard_build_sections' in your code to create a new section for dashboard.
</p>
<p>Your hook-function can be called by dashboard dispatcher in two modes 'setting' or 'dashboard'. Mode is passed in parameter $parms['mode'].<br />

    In 'setting' mode you have to add your own widget to $return variable with general info about widget but without content.<br />
    In 'dashboard' mode you have to check if your widget is active and prepare content.<br />

</p>

<p>See <font size='2'><b>core/addons/dashboard/sections/dashboard_sections_example.php</b></font> for example and skeleton of the new widget for dashboard.</p>

<p>Use menu <a href='index.php?target=dashboard'>General Settings &gt;&gt; Dashboard</a> to ebable/disable sections and define its order</p>
