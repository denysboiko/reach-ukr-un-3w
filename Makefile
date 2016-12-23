###############################################################################
# Image Commands
###############################################################################

# Create impact-initiatives/reach-ukr-un-3w image from python using Dockerfile
build:
	docker build -t impact-initiatives/reach-ukr-un-3w .

run:
	docker-compose up -d

status:
	docker ps
