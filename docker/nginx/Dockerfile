FROM nginx:alpine

# Remove default nginx config to avoid conflict
RUN rm -f /etc/nginx/conf.d/default.conf

# Copy app configuration directly into the image
COPY conf.d/app.conf /etc/nginx/conf.d/app.conf

# Copy entrypoint check script
COPY check-mounts.sh /docker-entrypoint.d/40-check-mounts.sh
RUN chmod +x /docker-entrypoint.d/40-check-mounts.sh
