<?php
class DependencyExclusion extends Exception
{
}
class NotSimilarNameException extends DependencyException
{
}
class NotHaveDependencyInName extends DependencyException
{
}
class NotJointDependencyInKey extends DependencyException
{
}
class ItselfDependency extends DependencyException
{
}
class CycleDependencyException extends DependencyException
{
} 
