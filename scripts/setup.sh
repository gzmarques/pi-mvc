#!/bin/bash

app_root_path="`pwd`"
site_root_path=$(echo $app_root_path | sed -e "s/\/var\/www//g")
htaccess_path="${app_root_path}/.htaccess"
application_path="${app_root_path}/config/application.php"
log_path="${app_root_path}/logs/PHP_errors.log"
site_name=$(basename $app_root_path)


function setup_config_application_path() {
  echo "setup SITE_ROOT path on $application_path"

  sed "s|define('SITE_ROOT'.*);|define('SITE_ROOT', '$site_root_path');|g" $application_path > "${application_path}.changed"
  mv "${application_path}.changed" $application_path
}

function setup_htaccess_auto_prepend_path() {
  echo "setup htaccess auto_prepend_path on $htaccess_path"

  sed "s|php_value auto_prepend_file.*|php_value auto_prepend_file '$application_path'|g" $htaccess_path > "${htaccess_path}.changed"
  mv "${htaccess_path}.changed" "${htaccess_path}"
}

function setup_htaccess_rewrite_base_path() {
  echo "setup RewriteBase path"
  sed "s|RewriteBase.*|RewriteBase '$site_root_path'|g" $htaccess_path > "${htaccess_path}.changed"
  mv "${htaccess_path}.changed" "${htaccess_path}"
}


function setup_htaccess_error_document_path() {
  echo "setup htaccess ErrorDocument on $htaccess_path"

  sed "s|ErrorDocument \([0-9]\{3\}\) .*|ErrorDocument \1 $site_root_path\/errors\/\1\.php|g" $htaccess_path > "${htaccess_path}.changed"
  mv "${htaccess_path}.changed" "${htaccess_path}"
}

function setup_log_error_path() {
  echo "setup log_error_path on $log_path"

  sed "s|php_value error_log .*|php_value error_log $log_path|g" $htaccess_path > "${htaccess_path}.changed"
  mv "${htaccess_path}.changed" "${htaccess_path}"
}

function fixed_log_permissions() {
  if [ -d "${app_root_path}/logs" ]; then
    echo "Change logs permission"
    cat /dev/null > "${app_root_path}/logs/application.log"
    cat /dev/null > "${app_root_path}/logs/PHP_errors.log"
    chmod -R 777 "${app_root_path}/logs"
  fi
}

function run() {
  setup_config_application_path
  setup_htaccess_auto_prepend_path
  setup_htaccess_rewrite_base_path
  setup_htaccess_error_document_path
  setup_log_error_path
  fixed_log_permissions
}

run
