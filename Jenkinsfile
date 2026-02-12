pipeline {
    agent any

    stages {
        stage('Build Docker Image') {
            steps {
                echo 'Building Docker image...'
                sh 'docker build -t my-php-app .'
            }
        }

        stage('Run Docker Container') {
            steps {
                echo 'Running Docker container...'
                sh '''
                    docker rm -f php-container || true
                    docker run -d -p 80:80 --name php-container my-php-app
                '''
            }
        }
    }
}
