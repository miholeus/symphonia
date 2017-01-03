FROM mysql:5.6

RUN apt-get update && \
    apt-get install less && \
    rm -rf /var/lib/apt/lists/*

RUN echo "\\\timing\n\\pset pager on\n\\setenv LESS '-iMSx4 -FX'\n" > ~/.mysqlrc
