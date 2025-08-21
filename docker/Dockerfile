FROM httpd:2.4

# Define user
ARG USER=appuser
ARG USER_ID=1000
ARG GROUP_ID=1000

RUN groupadd -g ${GROUP_ID} ${USER} \
    && useradd -m -u ${USER_ID} -g ${GROUP_ID} ${USER}

# Change ownership of the web and log directories so the user can access them.
RUN chown -R ${USER}:${USER} /usr/local/apache2/htdocs/
RUN chown -R ${USER}:${USER} /usr/local/apache2/logs/

# Enable SSI by loading the module directly in httpd.conf.
RUN echo 'LoadModule include_module modules/mod_include.so' >> /usr/local/apache2/conf/httpd.conf

# Enable the reading of .htaccess files.
RUN sed -i 's/AllowOverride None/AllowOverride All/' /usr/local/apache2/conf/httpd.conf

# Switch to the non-root user
USER ${USER}

EXPOSE 80