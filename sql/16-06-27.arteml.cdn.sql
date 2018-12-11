INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'opt_list_available_cdn_servers', 'List available CDN servers (one per line)', 'Requests to static content will be distributed across all listed domains. Explicitly add your main domain if you want to use it as well.', 'config');

INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'opt_use_cdn_for_https', 'Use CDN servers for HTTPs', 'You shall use CDN for HTTPs only if all your CDN servers have own valid SSL certificate', 'config');

select @cid:=config_category_id, @pos:=orderby FROM cw_config WHERE name='list_available_cdn_servers';
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES ('use_cdn_for_https', 'Use CDN for HTTPs', 'N', @cid, @pos+5, 'checkbox', 'N', '');
