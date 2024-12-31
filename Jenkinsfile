pipeline {
    agent any
    environment {
    DOCKER_REGISTRY = 'docker.io'                                 
    DOCKER_IMAGE = 'alibelkhaircontact1/akauting_app'             
    DOCKER_CREDENTIALS_ID = 'Docker_token'                     
  }

    stages {
        stage('SCM') {
            steps {
                checkout scm
            }
        }
        stage('SonarQube Analysis') {
            steps {
                script {
                    def scannerHome = tool 'SonarScanner'
                    withSonarQubeEnv() {
                        sh "${scannerHome}/bin/sonar-scanner"
                    }
                }
            }
        }
        stage('OWASP Dependency-Check Analysis') {
            steps {
                dependencyCheck additionalArguments: '--scan ./ --out ./dependency-check-report', odcInstallation: 'dependency-check'


                dependencyCheckPublisher pattern: 'dependency-check-report/dependency-check-report.xml'
            }
      
        }

    stage('Build Docker Image') {
      steps {
        echo 'Building Docker image...'
        script {
          def imageName = "${DOCKER_IMAGE}:${env.BUILD_NUMBER}"  
          docker.build(imageName)
          env.DOCKER_IMAGE_TAG = imageName                     
        }
      }
    }
    stage('Push Docker Image') {
      steps {
        echo 'Pushing Docker image to Docker Hub...'
        script {
          docker.withRegistry("https://${DOCKER_REGISTRY}", "${DOCKER_CREDENTIALS_ID}") {
            docker.image(env.DOCKER_IMAGE_TAG).push()
          }
        }
      }
    }
  }

  post {
    success {
      echo 'Pipeline completed successfully!'
    }

    failure {
      echo 'Pipeline failed. Please check the logs.'
    }
}
}